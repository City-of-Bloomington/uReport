drop table ticket_geodata;
drop table geoclusters;

create table geoclusters (
	id     int     unsigned not null primary key auto_increment,
	level  tinyint unsigned not null,
	center point            not null SRID 4326, -- EPSG WGS 84
	spatial index(center)
);

create table ticket_geodata (
	ticket_id    int unsigned not null primary key,
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
);
