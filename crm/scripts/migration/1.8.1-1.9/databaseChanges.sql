create table version (
	version varchar(8) not null primary key
);
insert version set version='1.9';

create table bookmarks (
	id          int unsigned not null primary key auto_increment,
	person_id   int unsigned not null,
	`type`      varchar(128) not null default 'search',
	name        varchar(128),
	requestUri  varchar(255) not null,
	foreign key (person_id) references people(id)
);

create table geoclusters (
	id int unsigned not null primary key auto_increment,
	level tinyint unsigned not null,
	center point not null,
	spatial index(center)

) engine=MyISAM;

create table ticket_geodata (
	ticket_id int unsigned not null primary key,
	cluster_id_0 int unsigned,
	cluster_id_1 int unsigned,
	cluster_id_2 int unsigned,
	cluster_id_3 int unsigned,
	cluster_id_4 int unsigned,
	cluster_id_5 int unsigned,
	cluster_id_6 int unsigned,
	foreign key (cluster_id_0) references geoclusters(id),
	foreign key (cluster_id_1) references geoclusters(id),
	foreign key (cluster_id_2) references geoclusters(id),
	foreign key (cluster_id_3) references geoclusters(id),
	foreign key (cluster_id_4) references geoclusters(id),
	foreign key (cluster_id_5) references geoclusters(id),
	foreign key (cluster_id_6) references geoclusters(id)
) engine=MyISAM;

update tickets set latitude=null,longitude=null
where latitude=0 or longitude=0;
