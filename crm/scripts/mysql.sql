-- @copyright 2006-2019 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
set foreign_key_checks=0;
create table version (
	version varchar(8) not null primary key
);
insert version set version='2.1';

create table departments (
	id               int          unsigned not null primary key auto_increment,
	name             varchar(128) not null,
	defaultPerson_id int          unsigned,
	constraint FK_departments_defaultPerson_id foreign key (defaultPerson_id) references people(id)
);

create table people (
	id                   int          unsigned not null primary key auto_increment,
	firstname            varchar(128),
	middlename           varchar(128),
	lastname             varchar(128),
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
	constraint FK_people_department_id foreign key (department_id) references departments(id)
);
set foreign_key_checks=1;

create table peopleEmails (
	id        int unsigned not null primary key auto_increment,
	person_id int unsigned not null,
	email     varchar(255) not null,
	label enum('Home','Work','Other') not null default 'Other',
	usedForNotifications tinyint(1) unsigned not null default 0,
	constraint FK_peopleEmails_person_id foreign key (person_id) references people(id)
);

create table peoplePhones (
	id        int          unsigned not null primary key auto_increment,
	person_id int          unsigned not null,
	number    varchar(20),
	label enum('Main', 'Mobile', 'Work', 'Home', 'Fax', 'Pager', 'Other') not null default 'Other',
	constraint FK_peoplePhones_person_id foreign key (person_id) references people(id)
);

create table peopleAddresses (
	id        int unsigned not null primary key auto_increment,
	person_id int unsigned not null,
	address   varchar(128) not null,
	city      varchar(128),
	state     varchar(128),
	zip       varchar(20),
	label enum('Home', 'Business', 'Rental') not null default 'Home',
	constraint FK_peopleAddresses_person_id foreign key (person_id) references people(id)
);

create table contactMethods (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);
insert into contactMethods set name='Email';
insert into contactMethods set name='Phone';
insert into contactMethods set name='Web Form';
insert into contactMethods set name='Other';

create table clients (
	id               int          unsigned not null primary key auto_increment,
	name             varchar(128) not null,
	url              varchar(255),
	api_key          varchar(50)  not null,
	contactPerson_id int          unsigned not null,
	contactMethod_id int          unsigned,
	constraint FK_clients_contactPerson_id foreign key (contactPerson_id) references people(id),
	constraint FK_clients_contactMethod_id foreign key (contactMethod_id) references contactMethods(id)
);

create table substatus (
	id          int          unsigned not null primary key auto_increment,
	name        varchar(25)  not null,
	description varchar(128) not null,
	status      enum('open', 'closed') not null default 'open',
	isDefault   bool not null default false
);
insert substatus (status, name, description) values('closed', 'Resolved', 'This ticket has been taken care of');
insert substatus (status, name, description) values('closed', 'Duplicate','This ticket is a duplicate of another ticket');
insert substatus (status, name, description) values('closed', 'Bogus',    'This ticket is not actually a problem or has already been taken care of');

create table actions (
	id          int          unsigned not null primary key auto_increment,
	name        varchar(25)  not null,
	description varchar(128) not null,
	type        enum('system', 'department') not null default 'department',
	template    text,
	replyEmail  varchar(128)
);
insert actions (name,type,description) values('open',           'system', 'Opened by {actionPerson}');
insert actions (name,type,description) values('assignment',     'system', '{enteredByPerson} assigned this case to {actionPerson}');
insert actions (name,type,description) values('closed',         'system', 'Closed by {actionPerson}');
insert actions (name,type,description) values('changeCategory', 'system', 'Changed category from {original:category_id} to {updated:category_id}');
insert actions (name,type,description) values('changeLocation', 'system', 'Changed location from {original:location} to {updated:location}');
insert actions (name,type,description) values('response',       'system', '{actionPerson} contacted {reportedByPerson_id}');
insert actions (name,type,description) values('duplicate',      'system', '{duplicate:ticket_id} marked as a duplicate of this case.');
insert actions (name,type,description) values('update',         'system', '{enteredByPerson} updated this case.');
insert actions (name,type,description) values('comment',        'system', '{enteredByPerson} commented on this case.');
insert actions (name,type,description) values('upload_media',   'system', '{enteredByPerson} uploaded an attachment.');

create table categoryGroups (
	id       int         unsigned not null primary key auto_increment,
	name     varchar(50) not null,
	ordering tinyint     unsigned
);
insert categoryGroups set name='Streets';
insert categoryGroups set name='Sanitation';
insert categoryGroups set name='Other';

create table categories (
	id                     int          unsigned not null primary key auto_increment,
	name                   varchar(50)  not null,
	description            varchar(512),
	department_id          int          unsigned not null,
	defaultPerson_id       int          unsigned,
	categoryGroup_id       int          unsigned,
	active                 boolean,
	featured               boolean,
	displayPermissionLevel enum('staff', 'public', 'anonymous') not null default 'staff',
	postingPermissionLevel enum('staff', 'public', 'anonymous') not null default 'staff',
	customFields           text,
	lastModified           timestamp    not null default CURRENT_TIMESTAMP,
	slaDays                int          unsigned,
	notificationReplyEmail varchar(128),
	autoCloseIsActive      bool,
	autoCloseSubstatus_id  int          unsigned,
	constraint FK_categories_department_id    foreign key (department_id)    references departments   (id),
	constraint FK_categories_defaultPerson_id foreign key (defaultPerson_id) references people        (id),
	constraint FK_categories_categoryGroup_id foreign key (categoryGroup_id) references categoryGroups(id)
);

create table category_action_responses (
    id int unsigned not null primary key auto_increment,
    category_id int unsigned not null,
    action_id   int unsigned not null,
    template    text,
    replyEmail  varchar(128),
    constraint FK_category_action_responses_category_id foreign key (category_id) references categories(id),
    constraint FK_category_action_responses_action_id   foreign key (action_id)   references actions   (id)
);

create table department_actions (
	department_id int unsigned not null,
	action_id     int unsigned not null,
	primary key (department_id, action_id),
	constraint FK_department_actions_department_id foreign key (department_id) references departments(id),
	constraint FK_department_actions_action_id     foreign key (action_id)     references actions    (id)
);

create table department_categories (
	department_id int unsigned not null,
	category_id   int unsigned not null,
	primary key (department_id, category_id),
	constraint FK_department_categories_department_id foreign key (department_id) references departments(id),
	constraint FK_department_categories_category_id   foreign key (category_id)   references categories (id)
);

create table tickets (
	id                  int         unsigned not null primary key auto_increment,
	parent_id           int         unsigned,
	category_id         int         unsigned,
	issueType_id        int         unsigned,
	client_id           int         unsigned,
	enteredByPerson_id  int         unsigned,
	reportedByPerson_id int         unsigned,
	assignedPerson_id   int         unsigned,
	contactMethod_id    int         unsigned,
	responseMethod_id   int         unsigned,
	enteredDate         datetime    not null default CURRENT_TIMESTAMP,
	lastModified        timestamp   not null default CURRENT_TIMESTAMP,
	addressId           int         unsigned,
	latitude            float(17, 14),
	longitude           float(17, 14),
	location            varchar(128),
	city                varchar(128),
	state               varchar(128),
	zip                 varchar(40),
	status              varchar(20) not null default 'open',
	closedDate          timestamp   null,
	substatus_id        int         unsigned,
	additionalFields    varchar(255),  -- Extra location fields from AddressService
	customFields        text,          -- Custom user-provided data defined in the Category
	description         text,
	constraint FK_tickets_parent_id          foreign key (parent_id)          references tickets    (id),
	constraint FK_tickets_category_id        foreign key (category_id)        references categories (id),
	constraint FK_tickets_client_id          foreign key (client_id)          references clients    (id),
	constraint FK_tickets_enteredByPerson_id foreign key (enteredByPerson_id) references people     (id),
	constraint FK_tickets_assignedPerson_id  foreign key (assignedPerson_id)  references people     (id),
	constraint FK_tickets_substatus_id       foreign key (substatus_id)       references substatus  (id)
);

create table issueTypes (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null
);
insert into issueTypes set name='Comment';
insert into issueTypes set name='Complaint';
insert into issueTypes set name='Question';
insert into issueTypes set name='Report';
insert into issueTypes set name='Request';
insert into issueTypes set name='Violation';

create table ticketHistory (
	id                 int unsigned not null primary key auto_increment,
	ticket_id          int unsigned not null,
	enteredByPerson_id int unsigned,
	actionPerson_id    int unsigned,
	action_id          int unsigned not null,
	enteredDate        timestamp    not null default CURRENT_TIMESTAMP,
	actionDate         datetime     not null default CURRENT_TIMESTAMP,
	notes              text,
	data               text,
	sentNotifications  text,
	constraint FK_ticketHistory_ticket_id          foreign key (ticket_id)          references tickets(id),
	constraint FK_ticketHistory_enteredByPerson_id foreign key (enteredByPerson_id) references people (id),
	constraint FK_ticketHistory_actionPerson_id    foreign key (actionPerson_id)    references people (id),
	constraint FK_ticketHistory_action_id          foreign key (action_id)          references actions(id)
);

create table media (
	id         int          unsigned not null primary key auto_increment,
	ticket_id  int          unsigned not null,
	filename   varchar(128) not null,
	internalFilename varchar(50) not null,
	mime_type  varchar(128),
	uploaded   timestamp    not null default CURRENT_TIMESTAMP,
	person_id  int          unsigned,
	constraint FK_media_ticket_id foreign key (ticket_id) references tickets(id),
	constraint FK_media_person_id foreign key (person_id) references people (id)
);

create table bookmarks (
	id          int unsigned not null primary key auto_increment,
	person_id   int unsigned not null,
	`type`      varchar(128) not null default 'search',
	name        varchar(128),
	requestUri  varchar(1024) not null,
	constraint FK_bookmarks_person_id foreign key (person_id) references people(id)
);

create table geoclusters (
	id     int     unsigned not null primary key auto_increment,
	level  tinyint unsigned not null,
	center point            not null SRID 0, -- Flatspace
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
