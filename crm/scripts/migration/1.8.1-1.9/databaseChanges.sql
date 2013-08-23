create table bookmarks (
	id          int unsigned not null primary key auto_increment,
	person_id   int unsigned not null,
	`type`      varchar(128) not null default 'search',
	name        varchar(128),
	requestUri  varchar(255) not null,
	foreign key (person_id) references people(id)
);
