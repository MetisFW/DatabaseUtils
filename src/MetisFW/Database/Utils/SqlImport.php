<?php

/**
 * This file is part of the MetisFW (http://github.com/MetisFW) *
 * Copyright (c) 2008 GrowJOB s.r.o. (developers@growjob.com) *
 * For the full copyright and license information, view the license file that was distributed with this source code.
 */

namespace Metis\Database\Utils;

use Metis\Database\Utils\Exceptions\FileNotFoundException;
use PDO;
use PDOException;
use PhpMyAdmin\SqlParser\Utils\BufferedQuery;

class SqlImport {

  /**
   * @param PDO $pdo Connection to database.
   * @param resource $stream Stream with SQL queries
   * @return int Count of executed queries
   */
  public static function loadfromStream(PDO $pdo, $stream) {
    return SqlImport::load($pdo, $stream, 'stream');
  }

  /**
   * @param PDO $pdo Connection to database.
   * @param string $sql String with SQL queries
   * @return int Count of executed queries
   * @throws FileNotFoundException If file does not exists
   */
  public static function loadfromString(PDO $pdo, $sql) {
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $sql);
    rewind($stream);
    $count = SqlImport::load($pdo, $stream, 'string');
    fclose($stream);
    return $count;
  }

  /**
   * @param PDO $pdo Connection to database.
   * @param string $file Path to SQL file
   * @return int Count of executed queries
   * @throws FileNotFoundException If file does not exists
   */
  public static function loadFromFile(PDO $pdo, $file) {
    set_time_limit(0);

    if(!is_readable($file)) {
      throw new FileNotFoundException("Cannot open SQL file '$file'.");
    }

    $stream = fopen($file, 'r');
    $count = SqlImport::load($pdo, $stream, "file '$file'");
    fclose($stream);

    return $count;
  }

  /**
   * @param PDO $pdo Connection to database.
   * @param resource $stream Stream with SQL queries
   * @return int Count of executed queries
   *
   * Based on implementation form nette/database
   */
  private static function load(PDO $pdo, $stream, $sourceName) {
    set_time_limit(0);

    $count = 0;
    $bq = new BufferedQuery();

    while (!feof($stream)) {
      $statement = $bq->extract();
      if (empty($statement)) {
        $newData = fgets($stream);
        $bq->query .= preg_replace("/\r($|[^\n])/", "\n$1", $newData);
        continue;
      }
      $count++;
      self::runQuery($pdo, $statement, $sourceName);
    }

    while (!empty($bq->query)) {
      $statement = $bq->extract(true);
      if (empty($statement)) {
        continue;
      }
      $count++;
      self::runQuery($pdo, $statement, $sourceName);
    }

    return $count;
  }

  private static function runQuery(PDO $pdo, $sql, $source) {
    try {
      $pdo->query($sql);
    }
    catch(PDOException $e) {
      $code = $e->getCode();
      if(!is_numeric($code)) {
        $code = 0;
      }
      throw new PDOException("Error in SQL query '$sql' from $source - " . $e->getMessage(), $code, $e);
    }
  }

}
