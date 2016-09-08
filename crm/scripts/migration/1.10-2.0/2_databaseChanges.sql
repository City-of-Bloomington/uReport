alter table categories add notificationReplyEmail varchar(128);
alter table categories add autoResponseIsActive   bool;
alter table categories add autoResponseText       text;
alter table categories add autoCloseIsActive      bool;
alter table categories add autoCloseSubstatus_id  int unsigned;
alter table categories add active                 boolean after categoryGroup_id;
alter table categories add featured               boolean after active;
alter table categories add defaultPerson_id       int unsigned;
alter table categories add constraint FK_categories_defaultPerson_id foreign key (defaultPerson_id) references people(id);
update categories set active=1;

create table category_action_responses (
    id int unsigned not null primary key auto_increment,
    category_id int unsigned not null,
    action_id   int unsigned not null,
    template    text,
    replyEmail  varchar(128),
    constraint FK_category_action_responses_category_id foreign key (category_id) references categories(id),
    constraint FK_category_action_responses_action_id   foreign key (action_id)   references actions   (id)
);

insert into category_action_responses (category_id, action_id, template, replyEmail)
select id, 5, autoResponseText, notificationReplyEmail
from categories where autoResponseText is not null;

alter table categories drop autoResponseIsActive;
alter table categories drop autoResponseText;

update ticketHistory h
join actions a on h.action_id=a.id
join actions b on b.name='assignment'
set h.action_id=b.id
where a.name='referral';

alter table tickets drop foreign key FK_tickets_referredPerson_id;
alter table tickets drop referredPerson_id;

delete from actions where name='referral';

alter table ticketHistory add issue_id int unsigned after ticket_id;
alter table ticketHistory add constraint FK_ticketHistory_issue_id foreign key (issue_id) references issues(id);
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
insert actions (name,type,description) values('update',         'system', '{enteredByPerson} updated this case.');
insert actions (name,type,description) values('comment',        'system', '{enteredByPerson} commented on this case.');
insert actions (name,type,description) values('upload_media',   'system', '{enteredByPerson} uploaded an attachment.');

delete from ticketHistory where action_id is null and notes is null;
delete from ticketHistory where action_id is null and notes='[data cleanup] Dates were missing and have been set to 1970';
update ticketHistory set action_id=(select id from actions where name='comment') where action_id is null;
alter table ticketHistory modify action_id int unsigned not null;

drop table issue_labels;
drop table labels;

alter table tickets add parent_id int unsigned after id;
alter table tickets add constraint FK_tickets_parent_id foreign key (parent_id) references tickets(id);

-- Move all merged issues onto seperate tickets, so that tickets
-- only have one issue per ticket;
--
-- The migration has a PHP script that does this
-- moveIssuesToDuplicateTickets.php
-- This select should return 0 results before moving on
select ticket_id, count(*) as c from issues group by ticket_id having c>1;
