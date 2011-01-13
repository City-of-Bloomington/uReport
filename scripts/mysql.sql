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
	department_id int unsigned,
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

create table constituents (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	middlename varchar(128),
	salutation varchar(4),
	address varchar(255),
	city varchar(128),
	state varchar(2),
	zip varchar(5),
	email varchar(255)
);
insert constituents set firstname='Anonymous',lastname='Anonymous';

create table constituentPhones (
	id int unsigned not null primary key auto_increment,
	label varchar(128),
	phoneNumber varchar(15) not null,
	constituent_id int unsigned not null,
	foreign key (constituent_id) references constituents(id)
);

create table issueTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table categories (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	department_id int unsigned not null,
	foreign key (department_id) references departments(id)
);

create table category_notes (
	id int unsigned not null primary key auto_increment,
	category_id int unsigned not null,
	note varchar(128),
	foreign key (category_id) references categories(id)
);

create table neighborhoodAssociations (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table issues (
	id int unsigned not null primary key auto_increment,
	type_id int unsigned not null,
	category_id int unsigned not null,
	constituent_id int unsigned not null,
	contactMethod_id int unsigned,
	address varchar(128),
	street_address_id int unsigned,
	township varchar(128),
	neighborhoodAssociation_id int unsigned,
	notes text,
	case_number varchar(10),
	lengthOfProblem varchar(25),
	foreign key (type_id) references issueTypes(id),
	foreign key (category_id) references categories(id),
	foreign key (constituent_id) references constituents(id)
);

create table actionTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);
insert actionTypes set name='Assigned';
insert actionTypes set name='Inspected';
insert actionTypes set name='Responded';
insert actionTypes set name='Resolved';

create table contactMethods (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);
insert contactMethods set name='Phone Call';
insert contactMethods set name='Letter';
insert contactMethods set name='Email';
insert contactMethods set name='Mayor Email';
insert contactMethods set name='Constituent Meeting';
insert contactMethods set name='Walk In';
insert contactMethods set name='Web Form';
insert contactMethods set name='Other';

create table actions (
	id int unsigned not null primary key auto_increment,
	issue_id int unsigned not null,
	actionType_id int unsigned not null,
	user_id int unsigned,
	target_user_id int unsigned,
	constituent_id int unsigned,
	department_id int unsigned,
	date date not null,
	contactMethod_id int unsigned,
	notes text,
	hours_spent double(4,1),
	foreign key (issue_id) references issues(id),
	foreign key (actionType_id) references actionTypes(id),
	foreign key (user_id) references users(id),
	foreign key (target_user_id) references users(id),
	foreign key (constituent_id) references constituents(id),
	foreign key (contactMethod_id) references contactMethods(id),
	foreign key (department_id) references departments(id)
);

create table media (
	id int unsigned not null primary key auto_increment,
	issue_id int unsigned not null,
	user_id int unsigned not null,
	filename varchar(128) not null,
	mime_type varchar(128) not null,
	media_type varchar(24) not null,
	title varchar(128),
	description varchar(255),
	md5 varchar(32) not null unique,
	uploaded timestamp not null default CURRENT_TIMESTAMP,
	notes text,
	foreign key (issue_id) references issues(id),
	foreign key (user_id) references users(id)
);
