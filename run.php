<?php

include_once "vendor/autoload.php";
include_once "classes/Mailer.php";
include_once "classes/Connection.php";

// stop if lock is set
if(file_exists("script.lock")) die("lock is up\n");

// create the lock file
file_put_contents("script.lock", "");

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
	if(empty($res))	break;
	$to = $res[0]->email;

	// get the email content
	$email = Connection::query("SELECT * FROM content WHERE active=1 ORDER BY RAND() LIMIT 1")[0];
	$id = $email->id;
	$subject = $email->subject;
	$body = $email->body;

	// send the email
	Mailer::send($to, $subject, $body);

	// save records
	Connection::query("
		UPDATE FROM content SET used=used+1 WHERE id=$id;
		UPDATE emails SET status='sent', processed=CURRENT_TIMESTAMP, content='$id' WHERE email='$to';")

	// save the log
	error_log("Email $id sent to $to at " . date("h:i:s"));

	// wait a random delay
	sleep(rand(2, 10));
}

// unlock the script
unlink("script.lock");
