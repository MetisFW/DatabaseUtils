SELECT t.table_name AS table_name
  FROM information_schema.tables t 
  WHERE 
    t.table_schema = DATABASE() AND
    t.table_type='BASE TABLE';
