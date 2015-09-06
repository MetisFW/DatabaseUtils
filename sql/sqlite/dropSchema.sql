PRAGMA writable_schema = 1;
DELETE FROM sqlite_master WHERE type = 'table';
PRAGMA writable_schema = 0;

VACUUM;

PRAGMA INTEGRITY_CHECK;
