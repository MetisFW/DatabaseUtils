<?php

/**
 * @dataProvider? ../databases.ini
 */

use Metis\Database\Utils\SqlImport;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */
$connection = require(__DIR__ . '/../connection.inc.php');

$count = SqlImport::loadFromFile($connection, __DIR__ . "/test.sql");
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query("SELECT * FROM `test-table` ORDER BY id")->fetchAll());

$count = SqlImport::loadFromString($connection, file_get_contents(__DIR__ . "/test.sql"));
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query("SELECT * FROM `test-table` ORDER BY id")->fetchAll());

$handler = fopen(__DIR__ . "/test.sql", "r");
$count = SqlImport::loadFromStream($connection, $handler);
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query("SELECT * FROM `test-table` ORDER BY id")->fetchAll());
fclose($handler);
