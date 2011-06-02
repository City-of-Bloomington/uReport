-- There's a lot of bad data entered into the system
-- Running these queries will go a long way to making sure it gets migrated consitently

-- Standardize how BPD has been entered
update ce_eng_comp
set last_name=null,address=null,city=null,state=null,zip_code=null
where first_name='BPD';
