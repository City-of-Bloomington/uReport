update ticketHistory set actionDate=enteredDate where actionDate=0;
update issueHistory  set actionDate=enteredDate where actionDate=0;
delete from ticketHistory where enteredDate='1970-01-01 00:00:00' and actionDate='1970-01-01 00:00:00';
update ticketHistory set enteredDate=actionDate where enteredDate='1970-01-01 00:00:00';
alter table tickets       modify enteredDate datetime /*!50700 not null default CURRENT_TIMESTAMP */;
alter table ticketHistory modify actionDate  datetime /*!50700 not null default CURRENT_TIMESTAMP */;
alter table issueHistory  modify actionDate  datetime /*!50700 not null default CURRENT_TIMESTAMP */;
