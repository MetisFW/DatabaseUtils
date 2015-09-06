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

SqlImport::loadFromFile($connection, __DIR__."/test.sql");
$tables = DatabaseManagement::listTables($connection);
Assert::same(array('test'), $tables);

SqlImport::loadFromFile($connection, __DIR__ . "/test.sql");
DatabaseManagement::deleteData($connection);
Assert::same(array(), $connection->query("SELECT * FROM test ORDER BY id")->fetchAll());

SqlImport::loadFromFile($connection, __DIR__."/test.sql");
DatabaseManagement::dropSchema($connection);
Assert::same(array(), DatabaseManagement::listTables($connection));
