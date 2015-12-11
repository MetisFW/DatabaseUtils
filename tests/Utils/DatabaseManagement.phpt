<?php

/**
 * @dataProvider? ../databases.ini
 */

use Metis\Database\Utils\DatabaseManagement;
use Metis\Database\Utils\SqlImport;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/** @var PDO $connection */
$connection = require(__DIR__ . '/../connection.inc.php');
$driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);

$fetchAllSql = file_get_contents(__DIR__."/fetchAll.{$driver}.sql");

// prepare empty database
DatabaseManagement::dropSchema($connection);

SqlImport::loadFromFile($connection, __DIR__."/test.{$driver}.sql");
$tables = DatabaseManagement::listTables($connection);
Assert::same(array('test-table'), $tables);
Assert::equal(array(
  array('id' => '100', '100'),
  array('id' => '101', '101'),
  array('id' => '102', '102')
), $connection->query($fetchAllSql)->fetchAll());;

SqlImport::loadFromFile($connection, __DIR__ . "/test.{$driver}.sql");
DatabaseManagement::deleteData($connection);
Assert::same(array('test-table'), $tables);
Assert::same(array(), $connection->query($fetchAllSql)->fetchAll());

SqlImport::loadFromFile($connection, __DIR__."/test.{$driver}.sql");
DatabaseManagement::dropSchema($connection);
Assert::same(array(), DatabaseManagement::listTables($connection));
