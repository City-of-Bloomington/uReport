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

alter table tickets add parent_id int unsigned after id;
alter table tickets add foreign key (parent_id) references tickets(id);
