SET @dbname = NULL;
SELECT database() into @dbname;

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE dropSchema_no_tables_fallback (id int)

SET @tables = NULL;
SELECT GROUP_CONCAT('`', table_schema, '.', table_name, '`') INTO @tables
  FROM information_schema.tables 
  WHERE table_schema = @dbname;

SET @tables = CONCAT('DROP TABLE IF EXISTS ', @tables);
PREPARE stmt FROM @tables;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
  
SET FOREIGN_KEY_CHECKS=1
