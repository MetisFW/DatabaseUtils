SELECT t.name AS table_name
 FROM sqlite_master AS t
 WHERE 
   t.type = 'table';
