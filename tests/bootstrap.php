<?php
// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .
if(@!include __DIR__.'/../vendor/autoload.php') {
  echo 'Install Nette Tester using `composer install`';
  exit(1);
}

// configure environment
\Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

// create temporary directory
define('TEMP_DIR', __DIR__.'/tmp/test'.getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
\Tester\Helpers::purge(TEMP_DIR);

function tableExists(PDO $pdo, $tableName) {

}

function test(\Closure $function) {
  before();
  $function();
}