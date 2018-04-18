#!/usr/bin/php -q
<?php

ini_set('display_errors', 1);

// stop if lock is set
$path = dirname(__FILE__);
if(file_exists("$path/script.lock")) die("lock is up\n");

include_once "vendor/autoload.php";
include_once "classes/Mailer.php";
include_once "classes/Connection.php";

// log when the crontab runs
error_log("[".date('Y-m-d H:i:s')."] TASK STARTED\n", 3, "$path/logs/event.log");

// create the lock file
file_put_contents("$path/script.lock", "");

while(true)
{
	// get the next email to use
	$res = Connection::query("
		SELECT id, email FROM emails
		WHERE status='waiting'
		AND inserted > (NOW() - INTERVAL 90 DAY)
		ORDER BY inserted ASC
		LIMIT 1");

	// ensure there is a next email
	if(empty($res)) break;
	$to = $res[0]->email;

	// check the email is valid
	if ( ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
	    Connection::query("UPDATE emails SET status='error', processed=CURRENT_TIMESTAMP WHERE email='$to'");
		continue;
	}

	// get the email content
	$email = Connection::query("SELECT * FROM content WHERE active=1 ORDER BY RAND() LIMIT 1")[0];
	$id = $email->id;
	$subject = $email->subject;
	$body = $email->body;

	// send the email
	try {
		Mailer::send($to, $subject, $body);
	} catch(Exception $e) {
		error_log("[".date('Y-m-d H:i:s')."] Error sending to $to. " . $e->getMessage() . "\n", 3, "$path/logs/error.log");
		break;
	}

	// save records
	Connection::query("UPDATE content SET used=used+1 WHERE id=$id;");
	Connection::query("UPDATE emails SET status='sent', processed=CURRENT_TIMESTAMP, content='$id' WHERE email='$to';");

	// save the log
	error_log("[".date('Y-m-d H:i:s')."] Email $id sent to $to\n", 3, "$path/logs/event.log");

	// wait a random delay
	sleep(rand(2, 10));
}

// unlock the script
unlink("$path/script.lock");
