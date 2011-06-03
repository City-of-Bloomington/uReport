-- There's a lot of bad data entered into the system
-- Running these queries will go a long way to making sure it gets migrated consitently

update ce_eng_comp
set last_name=null,address=null,city=null,state=null,zip_code=null
where first_name='BPD';

update ce_eng_comp
set e_mail_address='jackc@bloomington.in.gov',
address='401 N Morton St',city='Bloomington',state='IN',zip_code='47401'
where first_name='Carol' and last_name='Jack';