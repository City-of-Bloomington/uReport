uReport - Ansible
======================

The included ansible playbook and role will install uReport along with required dependencies.

These files also serve as living documentation of the system requirements and configurations necessary to run the application.

This assume some familiarity with the Ansible configuration management system and that you have an ansible control machine configured. Detailed instructions for getting up and running on Ansible are maintained as part of our system-playbooks repository:

https://github.com/City-of-Bloomington/system-playbooks

On the ansible control machine, make sure you have everything you need:

    git clone https://github.com/City-of-Bloomington/uReport
    cd uReport/ansible

Dependencies
-------------

Decide how you want to get the other necessary ansible roles:

    ansible-galaxy install -r roles.yml

or for development:

```
git clone https://github.com/City-of-Bloomington/ansible-role-linux.git ./roles/City-of-Bloomington.linux
git clone https://github.com/City-of-Bloomington/ansible-role-apache.git ./roles/City-of-Bloomington.apache
git clone https://github.com/City-of-Bloomington/ansible-role-mysql.git ./roles/City-of-Bloomington.mysql
git clone https://github.com/City-of-Bloomington/ansible-role-php.git ./roles/City-of-Bloomington.php
git clone https://github.com/City-of-Bloomington/ansible-role-solr.git ./roles/City-of-Bloomington.solr
```

Run the Playbook
-----------------

    ansible-playbook playbooks/uReport.yml -i hosts.txt

Additional Information
-------------------------
Did everything work as expected? If not, please let us know:

https://github.com/City-of-Bloomington/uReport/issues

This project and others like it are maintained on the City of Bloomington's Github page:

https://github.com/City-of-Bloomington

License
-------

Copyright (c) 2016 City of Bloomington, Indiana

This material is avialable under the GNU General Public License (GLP) v3.0:
https://www.gnu.org/licenses/gpl.txt


