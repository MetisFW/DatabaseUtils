# MetisFW/DatabaseUtils

## Usage

### Import SQL
```php
  \MetisFW\Database\Utils\SqlImport::loadFromFile($pdo, "data.sql")
```

```php
  \MetisFW\Database\Utils\SqlImport::loadFromString($pdo, "CREATE TABLE ...")
```

```php
  \MetisFW\Database\Utils\SqlImport::loadFromStream($pdo, fopen("php://stdin", "r")))
```

Note: SQL queries on multiple lines must by terminated by empty line

### List all tables in database

```php
  $tables = \MetisFW\Database\Utils\DatabaseManagement::listTables($pdo)
```

### Deleting data from all tables in database

```php
  \MetisFW\Database\Utils\DatabaseManagement::deleteData($pdo)
```

### Drop all tables in database

```php
  \MetisFW\Database\Utils\DatabaseManagement::dropSchema($pdo)
```