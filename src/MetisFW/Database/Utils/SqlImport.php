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

    $delimiter = ';';
    $count = 0;
    $lines = 0;
    $sql = '';
    while(!feof($stream)) {
      $line = rtrim(fgets($stream));
      $lines++;

      if(!strncasecmp($line, 'DELIMITER ', 10)) {
        $delimiter = substr($line, 10);
      } elseif(trim($line) == '' || ($lines == 1 && substr($line, -strlen($delimiter)) === $delimiter)) {
        // multi-line query ended by empty line or single line query terminated by delimiter
        $sql .= substr($line, 0, -strlen($delimiter));
        if(trim($sql) != '') {
          self::runQuery($pdo, $sql, $sourceName);
          $count++;
        }
        $sql = '';
        $lines = 0;
      } else {
        $sql .= $line."\n";
      }
    }

    if(trim($sql) !== '') {
      self::runQuery($pdo, $sql, $sourceName);
      $count++;
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
