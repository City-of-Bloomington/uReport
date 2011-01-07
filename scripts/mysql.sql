-- @copyright 2011 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table departments (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	default_user_id int unsigned not null
);

create table users (
	id int unsigned not null primary key auto_increment,
	username varchar(30) not null unique,
	password varchar(32),
	authenticationMethod varchar(40) not null default 'LDAP',
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	email varchar(255),
	department_id int unsigned not null,
	foreign key (department_id) references departments(id)
);

create table roles (
	id int unsigned not null primary key auto_increment,
	name varchar(30) not null unique
) engine=InnoDB;
insert roles values(1,'Administrator');

create table user_roles (
	user_id int unsigned not null,
	role_id int unsigned not null,
	primary key (user_id,role_id),
	foreign key(user_id) references users (id),
	foreign key(role_id) references roles (id)
);

create table issues (
	id int unsigned not null primary key auto_increment
);