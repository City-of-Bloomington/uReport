alter table categories drop featured;

alter table categories add temp_display enum('private', 'public') not null default 'private' after displayPermissionLevel;
alter table categories add temp_posting enum('private', 'public') not null default 'private' after postingPermissionLevel;
update categories set temp_display='private' where displayPermissionLevel='staff';
update categories set temp_posting='private' where postingPermissionLevel='staff';
update categories set temp_display='public'  where displayPermissionLevel='anonymous';
update categories set temp_posting='public'  where postingPermissionLevel='anonymous';
alter table categories drop displayPermissionLevel;
alter table categories drop postingPermissionLevel;
alter table categories rename column temp_display to displayPermissionLevel;
alter table categories rename column temp_posting to postingPermissionLevel;
