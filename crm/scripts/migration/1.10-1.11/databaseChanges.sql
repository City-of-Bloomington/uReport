alter table categories add notificationReplyEmail varchar(128);
alter table categories add autoResponseIsActive   bool;
alter table categories add autoResponseText       text;
alter table categories add autoCloseIsActive      bool;
alter table categories add autoCloseSubstatus_id  int unsigned;

create table category_action_responses (
    id int unsigned not null primary key auto_increment,
    category_id int unsigned not null,
    action_id   int unsigned not null,
    response    text,
    autoRespond bool,
    replyEmail  varchar(128),
    foreign key (category_id) references categories(id),
    foreign key (action_id)   references actions   (id)
);

insert into category_action_responses (category_id, action_id, response, autoRespond, replyEmail)
select id, 5, autoResponseText, autoResponseIsActive, notificationReplyEmail
from categories where autoResponseText is not null;

alter table categories drop notificationReplyEmail;
alter table categories drop autoResponseIsActive;
alter table categories drop autoResponseText;