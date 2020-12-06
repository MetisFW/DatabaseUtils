-- Install plpqsql if not exists
DELIMITER ;;
CREATE OR REPLACE FUNCTION create_language_plpgsql()
RETURNS BOOLEAN AS $$
    CREATE LANGUAGE plpgsql;
    SELECT TRUE;
$$ LANGUAGE SQL;;
DELIMITER ;

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
DELIMITER ;;
CREATE OR REPLACE FUNCTION deletedata() RETURNS void LANGUAGE plpgsql AS
$$
DECLARE row record;
BEGIN 
  FOR row IN 
    SELECT table_name 
      FROM information_schema.tables 
      WHERE 
        table_type = 'BASE TABLE' AND
        table_schema = 'public' 
  LOOP 
    EXECUTE 'TRUNCATE ' || quote_ident(row.table_name) || ' CASCADE';
  END LOOP;
END;
$$;;
DELIMITER ;

SELECT deletedata();

DROP FUNCTION deletedata();


