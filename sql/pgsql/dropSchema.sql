-- Install plpqsql if not exists
CREATE OR REPLACE FUNCTION create_language_plpgsql()
RETURNS BOOLEAN AS $$
    CREATE LANGUAGE plpgsql;
    SELECT TRUE;
$$ LANGUAGE SQL;

SELECT CASE WHEN NOT (
        SELECT  TRUE AS exists
        FROM    pg_language
        WHERE   lanname = 'plpgsql'
        UNION
        SELECT  FALSE AS exists
        ORDER BY exists DESC
        LIMIT 1
    )
THEN
    create_language_plpgsql()
ELSE
    FALSE
END AS plpgsql_created;

DROP FUNCTION create_language_plpgsql();

-- Delete all tables and types
CREATE OR REPLACE FUNCTION purgetestdb() RETURNS void LANGUAGE plpgsql AS
$$
DECLARE row record; --
BEGIN
  -- remove tables
  FOR row IN 
    SELECT table_name 
      FROM information_schema.tables 
      WHERE 
        table_type = 'BASE TABLE' AND
        table_schema = 'public' 
  LOOP 
    EXECUTE 'DROP TABLE ' || quote_ident(row.table_name) || ' CASCADE'; --
  END LOOP; --  
  -- remove types
  FOR row IN 
      SELECT      n.nspname as schema, t.typname as type 
        FROM        pg_type t 
        LEFT JOIN   pg_catalog.pg_namespace n ON n.oid = t.typnamespace 
        WHERE       (t.typrelid = 0 OR (SELECT c.relkind = 'c' FROM pg_catalog.pg_class c WHERE c.oid = t.typrelid)) 
        AND     NOT EXISTS(SELECT 1 FROM pg_catalog.pg_type el WHERE el.oid = t.typelem AND el.typarray = t.oid)
        AND     n.nspname NOT IN ('pg_catalog', 'information_schema')
    LOOP 
      EXECUTE 'DROP TYPE ' || quote_ident(row.type) || ' CASCADE'; --
    END LOOP; --
END; --
$$;

SELECT purgetestdb();

DROP FUNCTION purgetestdb();

