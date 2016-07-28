update ticketHistory set actionDate=enteredDate where actionDate=0;
update issueHistory  set actionDate=enteredDate where actionDate=0;
alter table tickets       modify enteredDate datetime not null default now();
alter table ticketHistory modify actionDate  datetime not null default now();
alter table issueHistory  modify actionDate  datetime not null default now();

--
-- This section should be reworked before release
-- These changes occurred in development versions over time
-- We can probably simplify this to just add the new table
-- in it's final form
alter table categories add notificationReplyEmail varchar(128);
alter table categories add autoResponseIsActive   bool;
alter table categories add autoResponseText       text;
alter table categories add autoCloseIsActive      bool;
alter table categories add autoCloseSubstatus_id  int unsigned;

create table category_action_responses (
    id int unsigned not null primary key auto_increment,
    category_id int unsigned not null,
    action_id   int unsigned not null,
    template    text,
    autoRespond bool,
    replyEmail  varchar(128),
    foreign key (category_id) references categories(id),
    foreign key (action_id)   references actions   (id)
);

insert into category_action_responses (category_id, action_id, template, autoRespond, replyEmail)
select id, 5, autoResponseText, autoResponseIsActive, notificationReplyEmail
from categories where autoResponseText is not null;

alter table categories drop autoResponseIsActive;
alter table categories drop autoResponseText;


update ticketHistory h
join actions a on h.action_id=a.id
join actions b on b.name='assignment'
set h.action_id=b.id
where a.name='referral';

alter table tickets drop foreign key tickets_ibfk_5;
alter table tickets drop referredPerson_id;

delete from actions where name='referral';


alter table ticketHistory add issue_id int unsigned after ticket_id;
alter table ticketHistory add foreign key (issue_id) references issues(id);
insert ticketHistory
    (    ticket_id,   issue_id,   enteredByPerson_id,   actionPerson_id,   action_id,   enteredDate,   actionDate,   notes)
select x.ticket_id, i.issue_id, i.enteredByPerson_id, i.actionPerson_id, i.action_id, i.enteredDate, i.actionDate, i.notes
from issueHistory i join issues x on i.issue_id=x.id;

drop table issueHistory;

alter table ticketHistory add data text;
insert actions (name,type,description) values('changeCategory', 'system', 'Changed category from {original:category_id} to {updated:category_id}');
insert actions (name,type,description) values('changeLocation', 'system', 'Changed location from {original:location} to {updated:location}');
insert actions (name,type,description) values('response',       'system', '{actionPerson} contacted {reportedByPerson_id}');
insert actions (name,type,description) values('duplicate',      'system', '{duplicate:ticket_id} marked as a duplicate of this case.');

delete h.* from ticketHistory h join actions a on h.action_id=a.id where a.name='update';
delete r.* from category_action_responses r join actions a on r.action_id=a.id where a.name='update';
delete from actions where name='update';

drop table issue_labels;
drop table labels;

-- ---------------------------
-- 2.0 Stuff
-- ---------------------------
alter table tickets add parent_id int unsigned after id;
alter table tickets add foreign key (parent_id) references tickets(id);

-- Move all merged issues onto seperate tickets, so that tickets
-- only have one issue per ticket;
--
-- select ticket_id, count(*) as c from issues group by ticket_id having c>1;
alter table tickets add        issueType_id int unsigned after        category_id;
alter table tickets add reportedByPerson_id int unsigned after enteredByPerson_id;
alter table tickets add    contactMethod_id int unsigned after  assignedPerson_id;
alter table tickets add   responseMethod_id int unsigned after   contactMethod_id;
alter table tickets add customFields text after additionalFields;
alter table tickets add description  text after customFields;

alter table tickets add foreign key (       issueType_id) references issueTypes    (id);
alter table tickets add foreign key (reportedByPerson_id) references people        (id);
alter table tickets add foreign key (   contactMethod_id) references contactMethods(id);
alter table tickets add foreign key (  responseMethod_id) references contactMethods(id);

update tickets t
join issues i on t.id=i.ticket_id
set t.issueType_id       = i.issueType_id,
    t.reportedByPerson_id= i.reportedByPerson_id,
    t.contactMethod_id   = i.contactMethod_id,
    t.responseMethod_id  = i.responseMethod_id,
    t.customFields       = i.customFields,
    t.description        = i.description;

alter table media add ticket_id int unsigned after issue_id;
alter table media add foreign key (ticket_id) references tickets(id);
update media m join issues i on m.issue_id=i.id set m.ticket_id=i.ticket_id;
alter table media modify ticket_id int unsigned not null;
alter table media drop foreign key media_ibfk_1;
alter table media drop issue_id;
