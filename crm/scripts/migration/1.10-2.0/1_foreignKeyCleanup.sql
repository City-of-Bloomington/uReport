-- This will generate all the MySQL commands to replace the existing foreign keys.
-- Save this output to a text file, so you can copy and paste these commands back into your MySQL client.
select concat(  'alter table ', TABLE_NAME, ' drop foreign key ', CONSTRAINT_NAME, ';\n',
              '  alter table ', TABLE_NAME, ' add constraint FK_', TABLE_NAME, '_', COLUMN_NAME,
              ' foreign key (',COLUMN_NAME, ') references ', REFERENCED_TABLE_NAME, '(', REFERENCED_COLUMN_NAME, ');')
from information_schema.key_column_usage
where CONSTRAINT_SCHEMA='crm'
and referenced_table_name is not null;