<?php

function load_env($path){
	if (!is_readable($path)) {
		die('Error: missing .env file');
	}
	$env = array();
	foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
			continue;
		}
		list($key, $value) = explode('=', $line, 2);
		$env[trim($key)] = trim(trim($value), '"\'');
	}
	return $env;
}

function database_connect(){
	$env = load_env(__DIR__.'/../.env');
	try {
		$db = new PDO('mysql:host='.$env['DB_HOST'].';port='.$env['DB_PORT'].';dbname='.$env['DB_NAME'], $env['DB_USER'], $env['DB_PASSWORD']);
		$db->exec("SET CHARACTER SET utf8");
	}
	catch(Exception $e) {
		die('Error: '.$e->getMessage());
	}
	return $db;
}
?>
