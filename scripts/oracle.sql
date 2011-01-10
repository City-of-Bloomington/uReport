-- @copyright 2009 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table people (
	id number primary key,
	firstname varchar2(128) not null,
	lastname varchar2(128) not null,
	email varchar2(255) not null
);

create sequence people_id_seq;

create trigger people_autoincrement_trigger
before insert on people
for each row
when (new.id is null)
begin
select people_id_seq.nextval INTO :new.id from dual;
end;
/

insert people (id,firstname,lastname,email) values(null,'Administrator','','');

create table users (
	id number primary key,
	person_id number not null unique,
	username varchar2(30) not null unique,
	password varchar2(32),
	authenticationmethod varchar2(40) default 'LDAP' not null,
	foreign key (person_id) references people(id)
);

create sequence users_id_seq;

create trigger users_autoincrement_trigger
before insert on users
for each row
when (new.id is null)
begin
select users_id_seq.nextval into :new.id from dual;
end;
/

insert users (id,person_id,username,password,authenticationmethod)
values(null,1,'admin',md5hash('admin'),'local');


create table roles (
	id number primary key,
	name varchar(30) not null unique
);

create sequence roles_id_seq;

create trigger roles_autoincrement_trigger
before insert on roles
for each row
when (new.id is null)
begin
select roles_id_seq.nextval into :new.id from dual;
end;
/

insert roles (id,name) values(null,'Administrator');

create table user_roles (
	user_id number not null,
	role_id number not null,
	primary key (user_id,role_id),
	foreign key(user_id) references users (id),
	foreign key(role_id) references roles (id)
);
insert user_roles (user_id,role_id) values(1,1);
