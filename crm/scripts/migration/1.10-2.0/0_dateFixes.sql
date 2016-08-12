update ticketHistory set actionDate=enteredDate where actionDate=0;
update issueHistory  set actionDate=enteredDate where actionDate=0;
alter table tickets       modify enteredDate datetime /*!50700 not null default CURRENT_TIMESTAMP */;
alter table ticketHistory modify actionDate  datetime /*!50700 not null default CURRENT_TIMESTAMP */;
alter table issueHistory  modify actionDate  datetime /*!50700 not null default CURRENT_TIMESTAMP */;
