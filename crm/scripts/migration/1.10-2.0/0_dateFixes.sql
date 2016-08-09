update ticketHistory set actionDate=enteredDate where actionDate=0;
update issueHistory  set actionDate=enteredDate where actionDate=0;
alter table tickets       modify enteredDate datetime not null default now();
alter table ticketHistory modify actionDate  datetime not null default now();
alter table issueHistory  modify actionDate  datetime not null default now();
