-- Move all merged issues onto seperate tickets, so that tickets
-- only have one issue per ticket;
--
-- The migration has a PHP script that does this
-- moveIssuesToDuplicateTickets.php
-- This select should return 0 results before moving on
select ticket_id, count(*) as c from issues group by ticket_id having c>1;


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
alter table media add constraint FK_media_ticket_id foreign key (ticket_id) references tickets(id);
update media m join issues i on m.issue_id=i.id set m.ticket_id=i.ticket_id;
alter table media modify ticket_id int unsigned not null;
alter table media drop foreign key FK_media_issue_id;
alter table media drop issue_id;


alter table responses add ticket_id int unsigned after issue_id;
alter table responses add constraint FK_responses_ticket_id foreign key (ticket_id) references tickets(id);
update responses r join issues i on r.issue_id=i.id set r.ticket_id=i.ticket_id;
alter table responses modify ticket_id int unsigned not null;
alter table responses drop foreign key FK_responses_issue_id;
alter table responses drop issue_id;


insert ticketHistory
    (    ticket_id, enteredByPerson_id,      actionPerson_id, enteredDate, actionDate,   notes, data, action_id)
select r.ticket_id, r.person_id,       t.reportedByPerson_id,      r.date,     r.date, r.notes,
       concat('{contactMethod_id:',r.contactMethod_id,'}') as data,
       (select id from actions where name='response')      as action_id
from responses r
join tickets t on r.ticket_id=t.id;

drop table responses;

alter table ticketHistory drop foreign key FK_ticketHistory_issue_id;
alter table ticketHistory drop issue_id;

drop table issues;

alter table media drop media_type;

alter table actions add template   text;
alter table actions add replyEmail varchar(128);

truncate table version;
insert version set version='2.0';
