<?php

include_once "vendor/autoload.php";
include_once "classes/Mailer.php";
include_once "classes/Connection.php";

$i = 1; // DELETE
while(true)
{
	// get the next email to use
	$res = Connection::query("SELECT id, email FROM emails WHERE status='waiting' ORDER BY inserted ASC LIMIT 1");
	$to = $res[0]->email;

	// get the email content
	// @TODO
	$id = "1234";
	$subject = "hello world";
	$body = "adios planeta";

	// send the email
	Mailer::send($to, $subject, $body);

	// save in the log
	// TODO
	$dt = date("h:i:s");
	error_log("Email $id sent to $to at $dt");

	// calculate a random delay
	sleep(rand(2, 10));

	// break on time
	if($i++ > 5) break; // DELETE
}

echo "ENDING";
