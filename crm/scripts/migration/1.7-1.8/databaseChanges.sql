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
alter table tickets add foreign key (substatus_id) references substatus(id);

-- Migrate customStatuses that have been used on tickets
insert into substatus (name, status, description)
select distinct status, 'open', '' from tickets where status not in ('open', 'closed');

update tickets t
set t.substatus_id=(select id from substatus s where s.name=t.status)
where t.status not in ('open', 'closed');

update tickets set status='open' where status not in ('open','closed');

alter table departments drop customStatuses;

------------------------------------------------
-- SLA Agreements
------------------------------------------------
alter table categories add slaDays int unsigned;

------------------------------------------------
-- Phones
------------------------------------------------
alter table phones add label enum('Main', 'Mobile', 'Work', 'Home', 'Fax', 'Pager', 'Other') not null default 'Other';
update phones set label='Other';
rename table phones to peoplePhones;

------------------------------------------------
-- Email split out into a separate table
------------------------------------------------
create table peopleEmails (
	id        int unsigned not null primary key auto_increment,
	person_id int unsigned not null,
	email     varchar(255) not null,
	label enum('Home','Work','Other') not null default 'Other',
	foreign key (person_id) references people(id)
);
update people set email=null where email='';
insert into peopleEmails (person_id, email) select id,email from people where email is not null;
alter table people drop email;

------------------------------------------------
-- People's addresses
------------------------------------------------
create table peopleAddresses (
	id        int unsigned not null primary key auto_increment,
	person_id int unsigned not null,
	address   varchar(128) not null,
	city      varchar(128),
	state     varchar(128),
	zip       varchar(20),
	label enum('Home', 'Business', 'Rental') not null default 'Home',
	foreign key (person_id) references people(id)
);
update people set address=null where address='';
insert into peopleAddresses (person_id,address,city,state,zip)
	select id,address,city,state,zip from people where address is not null;
alter table people drop address;

------------------------------------------------
-- Ticket modified and close dates
------------------------------------------------
alter table tickets modify enteredDate timestamp not null default 0;

alter table tickets add lastModified timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;
update tickets t set t.lastModified=(
	select max(h.actionDate) from ticketHistory h
	where t.id=h.ticket_id
);

alter table tickets add closedDate timestamp null;
update tickets t set t.closedDate=(
	select max(h.actionDate) from ticketHistory h,actions a
	where t.id=h.ticket_id and h.action_id=a.id and a.name='closed'
);