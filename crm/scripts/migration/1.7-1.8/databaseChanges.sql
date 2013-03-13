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
