-- @copyright 2006-2012 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
set foreign_key_checks=0;
create table departments (
	id               int          unsigned not null primary key auto_increment,
	name             varchar(128) not null,
	customStatuses   varchar(255),
	defaultPerson_id int          unsigned,
	foreign key (defaultPerson_id) references people(id)
);

create table people (
	id                   int          unsigned not null primary key auto_increment,
	firstname            varchar(128) not null,
	middlename           varchar(128),
	lastname             varchar(128) not null,
	email                varchar(255) not null,
	organization         varchar(128),
	address              varchar(128),
	city                 varchar(128),
	state                varchar(128),
	zip                  varchar(20),
	department_id        int          unsigned,
	username             varchar(40)  unique,
	password             varchar(40),
	authenticationMethod varchar(40),
	role varchar(30),
	foreign key (department_id) references departments(id)
);
set foreign_key_checks=1;

create table phones (
	id        int          unsigned not null primary key auto_increment,
	person_id int          unsigned not null,
	number    varchar(20),
	deviceId  varchar(128),
	foreign key (person_id) references people(id)
);

create table resolutions (
	id          int          unsigned not null primary key auto_increment,
	name        varchar(25)  not null,
	description varchar(128) not null
);
insert resolutions (name, description) values('Resolved', 'This ticket has been taken care of');
insert resolutions (name, description) values('Duplicate','This ticket is a duplicate of another ticket');
insert resolutions (name, description) values('Bogus',    'This ticket is not actually a problem or has already been taken care of');

create table actions (
	id          int          unsigned not null primary key auto_increment,
	name        varchar(25)  not null,
	description varchar(128) not null,
	type        enum('system', 'department') not null default 'department'
);
insert actions (name,type,description) values('open',      'system','Opened by {actionPerson}');
insert actions (name,type,description) values('assignment','system','{enteredByPerson} assigned this case to {actionPerson}');
insert actions (name,type,description) values('close',     'system','Closed by {actionPerson}');
insert actions (name,type,description) values('referral',  'system','{enteredByPerson} referred this case to {actionPerson}');

create table categoryGroups (
	id       int         unsigned not null primary key auto_increment,
	name     varchar(50) not null,
	ordering tinyint     unsigned not null default 0
);

create table categories (
	id                     int          unsigned not null primary key auto_increment,
	name                   varchar(50)  not null,
	description            varchar(128) not null,
	department_id          int          unsigned not null,
	categoryGroup_id       int          unsigned,
	displayPermissionLevel enum('staff', 'public', 'anonymous') not null default 'staff',
	postingPermissionLevel enum('staff', 'public', 'anonymous') not null default 'staff',
	foreign key (department_id)    references departments   (id),
	foreign key (categoryGroup_id) references categoryGroups(id)
);

create table department_actions (
	department_id int unsigned not null,
	action_id     int unsigned not null,
	primary key (department_id, action_id),
	foreign key (department_id) references departments(id),
	foreign key (action_id)     references actions    (id)
);

create table department_categories (
	department_id int unsigned not null,
	category_id   int unsigned not null,
	primary key (department_id, category_id),
	foreign key (department_id) references departments(id),
	foreign key (category_id)   references categories (id)
);

create table clients (
	id               int          unsigned not null primary key auto_increment,
	name             varchar(128) not null,
	url              varchar(255),
	api_key          varchar(50)  not null,
	contactPerson_id int          unsigned not null,
	foreign key (contactPerson_id) references people(id)
);

create table tickets (
	id                 int         unsigned not null primary key auto_increment,
	category_id        int         unsigned not null,
	client_id          int         unsigned,
	enteredByPerson_id int         unsigned,
	assignedPerson_id  int         unsigned,
	referredPerson_id  int         unsigned,
	enteredDate        timestamp   not null default CURRENT_TIMESTAMP,
	address_id         int         unsigned,
	latitude           float,
	longitude          float,
	location           varchar(128),
	city               varchar(128),
	state              varchar(128),
	zip                varchar(40),
	status             varchar(20) not null default 'open',
	resolution_id      int         unsigned,
	foreign key (category_id)        references categories (id),
	foreign key (client_id)          references clients    (id),
	foreign key (enteredByPerson_id) references people     (id),
	foreign key (assignedPerson_id)  references people     (id),
	foreign key (referredPerson_id)  references people     (id),
	foreign key (resolution_id)      references resolutions(id)
);

create table ticket_history (
	id                 int       unsigned not null primary key auto_increment,
	ticket_id          int       unsigned not null,
	enteredByPerson_id int       unsigned not null,
	actionPerson_id    int       unsigned,
	enteredDate        timestamp not null default CURRENT_TIMESTAMP,
	actionDate         timestamp,
	action             varchar(128),
	notes              text,
	foreign key (ticket_id)          references tickets(id),
	foreign key (enteredByPerson_id) references people (id),
	foreign key (actionPerson_id)    references people (id)
);

create table contactMethods (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table issueTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table labels (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);

create table issues (
	id                  int       unsigned not null primary key auto_increment,
	ticket_id           int       unsigned not null,
	contactMethod_id    int       unsigned,
	responseMethod_id   int       unsigned,
	issueType_id        int       unsigned,
	enteredByPerson_id  int       unsigned,
	reportedByPerson_id int       unsigned,
	date                timestamp not null default CURRENT_TIMESTAMP,
	description         text,
	customFields        text,
	foreign key (contactMethod_id)    references contactMethods(id),
	foreign key (responseMethod_id)   references contactMethods(id),
	foreign key (issueType_id)        references issueTypes    (id),
	foreign key (enteredByPerson_id)  references people        (id),
	foreign key (reportedByPerson_id) references people        (id)
);

create table issue_labels (
	issue_id int unsigned not null,
	label_id int unsigned not null,
	primary key (issue_id, label_id),
	foreign key (issue_id) references issues(id),
	foreign key (label_id) references labels(id)
);

create table issue_history (
	id                 int       unsigned not null primary key auto_increment,
	issue_id           int       unsigned not null,
	enteredByPerson_id int       unsigned not null,
	actionPerson_id    int       unsigned,
	enteredDate        timestamp not null default CURRENT_TIMESTAMP,
	actionDate         timestamp,
	action             varchar(128),
	notes              text,
	foreign key (issue_id)           references issues(id),
	foreign key (enteredByPerson_id) references people(id),
	foreign key (actionPerson_id)    references people(id)
);

create table media (
	id         int          unsigned not null primary key auto_increment,
	issue_id   int          unsigned not null,
	filename   varchar(128) not null,
	directory  varchar(255) not null,
	mime_type  varchar(128),
	media_type varchar(50),
	uploaded   timestamp    not null default CURRENT_TIMESTAMP,
	person_id  int          unsigned,
	foreign key (issue_id)  references issues(id),
	foreign key (person_id) references people(id)
);

create table responses (
	id               int       unsigned not null primary key auto_increment,
	issue_id         int       unsigned not null,
	date             timestamp not null default CURRENT_TIMESTAMP,
	contactMethod_id int       unsigned,
	notes            text,
	person_id        int       unsigned,
	foreign key (contactMethod_id) references contactMethods(id),
	foreign key (person_id)        references people        (id)
);
