SELECT t.table_name AS table_name
  FROM information_schema.tables AS t 
  WHERE 
    t.table_type = 'BASE TABLE' AND
    t.table_schema = 'public' 
