<?php

/**
 * @dataProvider? ../databases.ini
 */

use Metis\Database\Utils\SqlImport;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */
$connection = require(__DIR__ . '/../connection.inc.php');
$driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);

$fetchAllSql = file_get_contents(__DIR__."/fetchAll.{$driver}.sql");

$count = SqlImport::loadFromFile($connection, __DIR__ . "/test.{$driver}.sql");
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query($fetchAllSql)->fetchAll());

$count = SqlImport::loadFromString($connection, file_get_contents(__DIR__ . "/test.{$driver}.sql"));
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query($fetchAllSql)->fetchAll());

$handler = fopen(__DIR__ . "/test.{$driver}.sql", "r");
$count = SqlImport::loadFromStream($connection, $handler);
Assert::equal(5, $count);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query($fetchAllSql)->fetchAll());
fclose($handler);
