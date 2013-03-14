------------------------------------------------
-- Clients need to specify contactMethod_id
------------------------------------------------
alter table clients add contactMethod_id int unsigned not null;
update clients set contactMethod_id=(
	select ifnull((select id from contactMethods where name='Other'), 1)
);
alter table clients add foreign key (contactMethod_id) references contactMethods(id);


------------------------------------------------
-- Categories must track lastModified date
------------------------------------------------
alter table categories add lastModified timestamp not null default CURRENT_TIMESTAMP;
update categories set lastModified=now();

------------------------------------------------
-- Custom statuses and resolutions should become substatus
------------------------------------------------
-- First we need to deal with all the current resolutions
alter table resolutions add status enum('open', 'closed') not null default 'open';
rename table resolutions to substatus;
update substatus set status='closed';
update actions set name='closed' where name='close';

-- Warning - make sure you have the correct foreign key name here
-- show create table tickets\G
-- Look for the constraint name for the resolution_id FOREIGN KEY
alter table tickets drop foreign key tickets_ibfk_6;
alter table tickets change resolution_id substatus_id int unsigned;

-- Migrate customStatuses that have been used on tickets
insert into substatus (name, status, description)
select distinct status, 'open', '' from tickets where status not in ('open', 'closed');

update tickets t
set t.substatus_id=(select id from substatus s where s.name=t.status)
where t.status not in ('open', 'closed');

update tickets set status='open' where status not in ('open','closed');

alter table departments drop customStatuses;