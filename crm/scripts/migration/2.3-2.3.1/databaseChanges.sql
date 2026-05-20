alter table peoplePhones drop deviceId;
alter table categories modify description varchar(512);

alter table people drop password;
alter table people drop authenticationMethod;
