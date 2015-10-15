alter table categories add autoResponseIsActive  bool;
alter table categories add autoResponseText      text;
alter table categories add autoCloseIsActive     bool;
alter table categories add autoCloseSubstatus_id int unsigned;
