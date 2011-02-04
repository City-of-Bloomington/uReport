-- @copyright 2006-2010 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>

/*! set foreign_key_checks=0 */;
create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	middlename varchar(128),
	lastname varchar(128),
	email varchar(255),
	phone varchar(30),
	address varchar(128),
	-- The rest of these fields are used as cache
	-- This information will ultimately come from other applications webservices
	street_address_id int unsigned,
	subunit_id int unsigned,
	neighborhoodAssociation varchar(128),
	township varchar(128)
);

create table departments (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	default_person_id int unsigned not null,
	foreign key (default_person_id) references people(id)
);

create table users (
	id int unsigned not null primary key auto_increment,
	person_id int unsigned not null unique,
	username varchar(30) not null unique,
	password varchar(32),
	authenticationMethod varchar(40) not null default 'LDAP',
	department_id int unsigned,
	foreign key (person_id) references people(id),
	foreign key (department_id) references departments(id)
);

create table roles (
	id int unsigned not null primary key auto_increment,
	name varchar(30) not null unique
);
insert roles set name='Administrator';

create table user_roles (
	user_id int unsigned not null,
	role_id int unsigned not null,
	primary key (user_id,role_id),
	foreign key(user_id) references users (id),
	foreign key(role_id) references roles (id)
);

create table categories (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table category_notes (
	id int unsigned not null primary key auto_increment,
	category_id int unsigned not null,
	note varchar(128),
	foreign key (category_id) references categories(id)
);

create table department_categories (
	department_id int unsigned not null,
	category_id int unsigned not null,
	foreign key (department_id) references departments(id),
	foreign key (category_id) references categories(id),
	primary key (department_id,category_id)
);

create table tickets (
	id int unsigned not null primary key auto_increment,
	date date not null,
	person_id int unsigned,
	location varchar(128),
	-- The rest of these fields are used as cache
	-- This information will ultimately come from other applications webservices
	street_address_id int unsigned,
	subunit_id int unsigned,
	neighborhoodAssociation varchar(128),
	township varchar(128),
	latitude decimal(8,6),
	longitude decimal(8,6),
	foreign key (person_id) references people(id)
);

create table issueTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table contactMethods (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table issues (
	id int unsigned not null primary key auto_increment,
	date date not null,
	ticket_id int unsigned not null,
	issueType_id int unsigned not null,
	constituent_id int unsigned,
	contactMethod_id int unsigned,
	person_id int unsigned,
	notes text,
	case_number varchar(10),
	foreign key (ticket_id) references tickets(id),
	foreign key (issueType_id) references issueTypes(id),
	foreign key (constituent_id) references people(id),
	foreign key (contactMethod_id) references contactMethods(id),
	foreign key (person_id) references people(id)
);

create table issue_categories (
	issue_id int unsigned not null,
	category_id int unsigned not null,
	foreign key (issue_id) references issues(id),
	foreign key (category_id) references categories(id),
	primary key (issue_id,category_id)
);

create table actionTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	verb varchar(128) not null
);

create table actions (
	id int unsigned not null primary key auto_increment,
	actionType_id int  unsigned not null,
	date date not null,
	ticket_id int unsigned not null,
	person_id int unsigned not null,
	targetPerson_id int unsigned,
	notes text,
	foreign key (actionType_id) references actionTypes(id),
	foreign key (ticket_id) references tickets(id),
	foreign key (person_id) references people(id),
	foreign key (targetPerson_id) references people(id)
);

/*! set foreign_key_checks=1 */;
