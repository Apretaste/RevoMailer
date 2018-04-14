<?php

class Mailer
{
	static function send($to, $subject, $body)
	{
		// include the config params
		require "configs/configs.php";

		// create message
		$mail = new Nette\Mail\Message;
		$mail->setFrom("noreply@$domain");
		$mail->addTo($to);
		$mail->setSubject($subject);
		$mail->setHtmlBody($body, false);

		// send mail
		$mailer = new Nette\Mail\SmtpMailer([
			'host' => $host,
			'username' => $username,
			'password' => $password,
			'port' => $port,
			'secure' => $secure
		]);

		$mailer->send($mail, false);

		return true;
	}
}
