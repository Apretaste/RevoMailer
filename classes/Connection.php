<?php

chdir(dirname(__FILE__));
class Connection
{
	public static function query($sql)
	{
		// include the config params
		require "../configs/configs.php";

		// connect to the database
		$mysqli = new mysqli("127.0.0.1", "root", $mysqlpass, "revomailer");
		$res = $mysqli->query($sql);

		// only fetch for selects
		if(stripos(trim($sql), "select") === 0)
		{
			$arr = [];
			while ($row = $res->fetch_object()) $arr[] = $row;
			$res->close();
			return $arr;
		}

		return true;
	}
}
