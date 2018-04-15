<?php

$path = dirname(__FILE__);
include_once "$path/classes/Connection.php";

class Mailer
{
	static function send($to, $subject, $body)
	{
		// include the config params
		require "$path/configs/configs.php";

		// get the domain to use
		$domain = Connection::query("SELECT domain FROM domains WHERE active=1 ORDER BY RAND() LIMIT 1")[0]->domain;
		Connection::query("UPDATE domains SET used=used+1 WHERE domain='$domain'");

		// get the word to use in the email
		$words = ['absorbente','afincar','ahora','ahorita','ajiaco','ajustador','almendron','apear','arepa','acere','bacan','baroco','bejuco','bemba','bembo','bisne','bisnero','blumer','bochinche','bodega','boniato','botella','brete','bruja','caballito','cabron','cagua','caldosa','camajan','camello','cana','candela','canonero','canona','carapacho','cartucho','cobio','coladito','colirio','comecandela','comemierda','conuco','consorte','cono','corduroi','chabacaneria','chalana','chama','chapistear','chavito','chivo','cherna','chiflido','chispetren','chocha','cuca','curralo','curda','cutara','descarga','despelote','digui','embalado','entufado','fajarse','fanoso','faronalla','federico','federica','fiana','fines','fongo','fregar','fria','frio','frutabomba','fuetazo','fula','gago','galleta','ganso','gao','garaje','garita','goma','grillo','guacha','guachipupa','guagua','guaguanco','guaguero','guajiro','guapo','guarachar','guarapito','guarapo','guataca','guineo','guisazo','jaba','jaiba','jalao','jama','jamar','jeva','jicotea','jinetera','jodedor','juaniquiqui','lague','maja','malanga','mamirriqui','maquinon','masetero','mate','monja','momentico','monina','mono','name','jajaro','papaya','pastilla','pato','paladar','palestina','palo','papiar','parra','pasmado','pasta','patatus','patente','papirriqui','pepilla','perfilo','perga','perseguidora','pescado','pestillo','pico','picuda','pimpampum','pincha','pincho','pinga','pinazo','pitusa','prajo','pullover','punto','pura','puro','quimbar','quimbao','quimbombo','quitrin','rabo','reyoya','rufa','saltapatra','saoco','singar','saya','sayan','sayuela','taco','taleo','tarudo','telegrama','temba','tembleque','templar','tembana','timon','tiradera','tortillera','venao','ventana','verraco','vianda','vola','walfarina','yegua','yuca','yuma'];
		$word = $words[array_rand($words)];

		// create message
		$mail = new Nette\Mail\Message;
		$mail->setFrom("$word@$domain");
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
