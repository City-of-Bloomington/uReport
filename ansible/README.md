uReport - Ansible
======================

The included ansible playbook and role will install uReport along with required dependencies.

These files also serve as living documentation of the system requirements and configurations necessary to run the application.

This assume some familiarity with the Ansible configuration management system and that you have an ansible control machine configured. Detailed instructions for getting up and running on Ansible are maintained as part of our system-playbooks repository:

https://github.com/City-of-Bloomington/system-playbooks

On the ansible control machine, make sure you have everything you need:

    git clone https://github.com/City-of-Bloomington/uReport
    cd uReport/ansible

Variables
-------------
### Solr
Solr 7.4.0 is the very latest version known to work with uReport.  If you are deploying to multiple hosts, you might consider hosting the package on a local webserver.  Apache's hosting can be very slow.

    solr_version: "7.4.0"
    solr_download_url: "http://packages.bloomington.in.gov/Apache/{{ solr_filename }}.tgz"

### Installation paths
The archive path is the path to the tarball you downloaded.  If you cloned from Github, you'll need to do a build to create the release archive.

    ureport_archive_path: ../build/ureport.tar.gz
    ureport_install_path: "/srv/sites/ureport"
    ureport_backup_path:  "/srv/backups/ureport"
    ureport_site_home:    "/srv/data/ureport"

### Apache configuration
The max image size is the largest upload file size accepted.  Users will not be able to upload images larger than this size.

    ureport_base_uri: /ureport
    ureport_base_url: https://{{ ansible_host }}{{ ureport_base_uri }}
    ureport_max_image_size: 10M

### Database
You should vault the database password.

    ureport_db:
    name:     "ureport"
    username: "ureport"
    password: "{{ vault_ureport_db.password }}"

## Google Api Key
You should vault your google api key.

    ureport_google_api_key: "{{ vault_google_api_key }}"

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

    ansible-playbook deploy.yml -i hosts.txt

Additional Information
-------------------------
Did everything work as expected? If not, please let us know:

https://github.com/City-of-Bloomington/uReport/issues

This project and others like it are maintained on the City of Bloomington's Github page:

https://github.com/City-of-Bloomington

License
-------

Copyright (c) 2016-2017 City of Bloomington, Indiana

This material is avialable under the GNU General Public License (GLP) v3.0:
https://www.gnu.org/licenses/gpl.txt
