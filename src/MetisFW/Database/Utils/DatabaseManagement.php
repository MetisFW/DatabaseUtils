<?php

/**
 * This file is part of the MetisFW (http://github.com/MetisFW) *
 * Copyright (c) 2008 GrowJOB s.r.o. (developers@growjob.com) *
 * For the full copyright and license information, view the license file that was distributed with this source code.
 */

namespace Metis\Database\Utils;

use Metis\Database\Utils\Exceptions\NotSupportedException;
use PDO;

class DatabaseManagement {

  const SQL_DIR = "../../../../sql";

  /**
   * Delete all data from database
   * @param PDO $pdo Database connection
   * @return void
   */
  public static function deleteData(PDO $pdo) {
     self::runCommand($pdo, 'deleteData');
  }

  /**
   * Drop all tables from database
   * @param PDO $pdo Database connection
   * @return void
   */
  public static function dropSchema(PDO $pdo) {
    self::runCommand($pdo, 'dropSchema');
  }
  
  /**
   * Return names of all tables in database
   * @param PDO $pdo Database connection
   * @return string[]
   */
  public static function listTables(PDO $pdo) {
    $result = $pdo->query(file_get_contents(self::getCommandSqlScript($pdo, 'listTables')));
    return $result->fetchAll(PDO::FETCH_COLUMN, 0);
  }
  
  private static function runCommand(PDO $pdo, $command) {
    $scriptFile = self::getCommandSqlScript($pdo, $command);
    if(is_file($scriptFile)) {
      SqlImport::loadFromFile($pdo, $scriptFile);
      return;
    }
    $statementFile = self::getCommandSqlStatement($pdo, $command);
    if(is_file($statementFile)) {
      $statement = file_get_contents($statementFile);
      foreach (self::listTables($pdo) as $tableName) {
        $tableStatement = str_replace("%table_name%", $pdo->quote($tableName), $statement);
        $pdo->exec($tableStatement);
      }
      return;
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    throw new NotSupportedException("Command $command not supported for $driver database.");
  }

  private static function getCommandSqlScript(PDO $pdo, $command) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    return __DIR__ . '/' . self::SQL_DIR . "/$driver/$command.sql";
  }

  private static function getCommandSqlStatement(PDO $pdo, $command) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    return __DIR__ . '/' . self::SQL_DIR . "/$driver/$command.sql.statement";
  }

}
