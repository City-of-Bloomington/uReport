-- Invalid Email Addresses
select e.person_id, e.email
from peopleEmails e
where email not regexp "^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$";


-- People with no activity
select p.id,
       count(e.id) as entered,
       count(r.id) as reported,
       count(a.id) as assigned,
       count(he.id) as historyEntered,
       count(ha.id) as historyAction
from people p
left join tickets        e on p.id=e.enteredByPerson_id
left join tickets        r on p.id=r.reportedByPerson_id
left join tickets        a on p.id=a.assignedPerson_id
left join ticketHistory he on p.id=he.enteredByPerson_id
left join ticketHistory ha on p.id=ha.actionPerson_id
group by p.id
having entered=0;

select p.id,
       count(e.id) as entered,
       count(r.id) as reported,
       count(a.id) as assigned
from people p
left join tickets        e on p.id=e.enteredByPerson_id
left join tickets        r on p.id=r.reportedByPerson_id
left join tickets        a on p.id=a.assignedPerson_id
group by p.id
having entered=0;
