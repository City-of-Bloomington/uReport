alter table substatus add isDefault bool not null default false;
alter table clients modify contactMethod_id int unsigned;
