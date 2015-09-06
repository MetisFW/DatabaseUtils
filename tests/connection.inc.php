<?php

try {
	$options = \Tester\Environment::loadData() + array('user' => NULL, 'password' => NULL);
} catch (Exception $e) {
	\Tester\Environment::skip($e);
}

try {
	$connection = new PDO($options['dsn'], $options['user'], $options['password']);
} catch (PDOException $e) {
	\Tester\Environment::skip("Connection to '$options[dsn]' failed. Reason: " . $e);
}

\Tester\Environment::lock($options['dsn'], TEMP_DIR . '/..');

$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);

return $connection;
