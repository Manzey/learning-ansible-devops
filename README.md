# Report

## Team - Grupp 3
- Markus Medin - mm223bw@student.lnu.se
- David Larsson - dl222is@student.lnu.se
- Tobias Bernting - tb222md@student.lnu.se

## Introduction
The company ACME Corporation are looking to scale their website to handle more traffic. They are also looking for a solution to automate the configuration and deployement of the infastructure. We have worked up a solution to solve their problems. We've created an automated way of rebuilding the whole infrastructure, including networks and related security groups. We've also implemented a backup system, which gives ACME the possibility of rebuilding their infrastructure whenever needed without losing data.

## Solution
The solution is described in this [document](docs/Part1.md).

### Intro
When running playbooks, almost all playbooks need to access the ansible vault. Append --ask-vault-pass to enter password or use --vault-password-file FILE to read from a file on all commands containing the provisioning playbooks. Eg:

```ansible-playbook site.yml --vault-password-file <vaultpwdfile>```
### How does the system handle changeability?
- There are two ways to install security upgrades
  1. Run the site.yml playbook to run all server playbooks and it will install security updates if any. Alternative is to run a specific playbook, say loadbalancer.yml to only update the loadbalancers.
  2. The preffered way is to destroy a instance before updating, to avoid configurational drift. Run destroy_instances.yml --tag <b>ACME_xx</b> to destroy a server and then run create-instances.yml and the playbook of the server that is to be provisioned.

  Let's say that there's a new security update available regarding some HTTP stuff, and we would like to apply that to the Loadbalancers. First, destroy the <b>slave</b> loadbalancer, rebuild and reprovision and run tests. Second, destroy the <b> master</b> loadbalancer, rebuild and provision and run tests. Doing it this way means that there will be no downtime of the application.

  ``` ansible-playbook destroy_instance.yml --tag ACME_lb2 && ansible-playbook create-instances.yml && ansible-playbook loadbalancer.yml && ansible-playbook test/test.yml --tag loadbalancer```

#### Upgrade Wordpress
1. First find the direct download for the Wordpress version you wish to upgrade to.
2. Navigate to group_vars/wordpress.yml.
3. Change the variable named wordpress_url to the direct download for your desired Wordpress version.
```yml
  wordpress_url: DIRECT_DOWNLOAD_URL_HERE
```
4. Wordpress will now be updated to the desired version upon rebuild.

#### Upgrade to Ubuntu 18.04
1. First find and get the Ubuntu image from openstack and add it to group_vars/all.yml
2. Change the image variable in roles/openstack-instances/vars/main.yml on the desired server in the server list.
```yml
  - name: lb2
    image: "{{ubuntu_image_1804}}" <- HERE
    flavor: "{{lb_flavor}}"
    key: "{{keyname}}"
    security_groups: "{{lb_sg}}"
    availability_zone: "{{lb_zone}}"
    wait: yes
    auto_ip: no
    meta:
      group: loadbalancers, production
      fixed_ip: "{{ lb2_fixed_ip }}"
```
3. Run ```ansible-playbook destroy_instance.yml --tag ACME_SERVER-TO-DESTROY```
4. Create the server again: ``` ansible-playbook create-instances.yml ```
5. Run provisioning
    - With --limit on the main playbook: ```ansible-playbook site.yml --limit SERVERGROUP```
    - Run the server playbook directly:
    ```ansible-playbook servergroupname.yml```
6. Run tests
    - Just the server group ``` ansible-playbook test/main.yml --tag SERVERGROUPNAME```
    - All tests ```ansible-playbook test/main.yml```

#### Upgrade demo (Click image to get redirected to video)
<a href="http://www.youtube.com/watch?feature=player_embedded&v=bMVMdoe4vbc
" target="_blank"><img src="http://img.youtube.com/vi/bMVMdoe4vbc/0.jpg" 
alt="Upgrade Demo" width="480" height="360" border="10" /></a>

### How does your team work with version control tools?
We have worked with Git for version control. And in-order to avoid merge conflicts we have worked with branching and we all worked in a similar way and could therefore avoid most of the merge conflicts. They still occured, but were not complicated to fix and was mostly just Gits automatic merge system that could not deal with difference in lines and linebreaks.

#### How we commit work
We follow the practice of never commiting directly to master, but instead we create a new branch where we performed a series of tasks and then merge the new branch into the master branch.
1. Create new a new branch.
2. Perform your modifications to the newly created branch.
3. When ready, pull request from master and Git will inform if there is any merge conflicts. If there are, you can fix them directly in the pull request.
4. Sucessfully merge your work into master.
5. Delete the "newly" created branch when you're done.

### Recreation
#### Recreation demo (Click image to get redirected to video)
<a href="http://www.youtube.com/watch?feature=player_embedded&v=SFqXExpluss
" target="_blank"><img src="http://img.youtube.com/vi/SFqXExpluss/0.jpg" 
alt="Recreation Demo" width="480" height="360" border="10" /></a>

#### How the system works
![alt text][Arch]   
Our solution is based on the attached sketch above.  
The user is connecting to the active loadbalancer, and in case the first loadbalancer is not working responsive the passive loadbalancer will reconfigure itself to become primary. Once the user is connected to a loadbalancer the user will be forwarded to the Wordpress instance with least connections.   

The Wordpress servers are communicating with the database and have a mounted disk from the fileserver. Once an hour the Wordpress servers are taking a snapshot of the database and the mounted disk, these files are then copied over to the backup server every hour.
In addition to this the solution includes a jumpserver where all provisioning is performed, this is to provide a more safe solution and none of the production servers are directly accessible with SSH connection.   

The monitoring is setup with a combination of Sensu and Grafana, one dedicated monitoring server called monitor is configured with all required services installed on it, all instances in this solution also has an installed Sensu client that is performing the checks and measurements. In order to access Uchiwa (Sensu dashboard) the following URL is used [http://194.47.206.137](http://194.47.206.137 "Uchiwa") and Grafana is accessed from URL [http://194.47.206.137:3000](http://194.47.206.137:3000), all warnings and errors that are detected by the system are presented in a slack channel [#acme_monitoring](https://coursepress.slack.com/messages/GDELX5F6W). A more detailed explanation of the monitoring can be seen in the monitoring demo. The generic checks and measurements that are performed on all the instances are CPU usage, Memory usage and Disk usage both in checks and measurements presented in graphs. More specialized monitoring is used on the loadbalancers and database server. 

##### Monitor Demo (Click image to get redirected to video)
[![Monitor Demo](http://i3.ytimg.com/vi/uq3sI3j17VY/hqdefault.jpg)](https://youtu.be/uq3sI3j17VY "Monitor Demo")

[Arch]: https://github.com/2dv514/Grupp03-examination-ht18/blob/master/docs/Arch.png "Architecture"
#### First time setup
1. In the Openstack project, create three floating ips, one for the jumpserver, one for the monitoring and one for the loadbalancers.
2. Add the jumpserver floating IP to the ansible.cfg file (eg: ubuntu@194.47.206.46):
3. Add your location to the private key used to create instances in openstack here as well (eg: ~/.ssh/openstack.pem):
```cfg
[ssh_connection]
ssh_args='-C -o ControlMaster=auto -o ControlPersist=60s -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o ProxyCommand="ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -W %h:%p -q -i PATH/TO/KEY ubuntu@JUMPSERVER-FLOATING-IP-HERE"'
```
4. In group_vars/all.yml change the js_public, mon_public and lb_public variables to the created floating ips.
```yml
# Loadbalancer
lb_hostname: "ACME_LoadBalancer"
lb_flavor: "c1-r05-d10"
lb_sg: "SSHInt,HTTP,vrrp-sec-grp"
lb_zone: "Education"
lb_vip: 192.168.0.223
lb1_fixed_ip: 192.168.0.12
lb2_fixed_ip: 192.168.0.13
lb_public: 194.47.206.156 <-- HERE

#monitoring
mon_hostname: "ACME_Monitor"
mon_flavor: "c1-r4-d20"
mon_sg: "SSHInt,Monitor"
mon_zone: "Education"
mon_fixed_ip: 192.168.0.10
mon_public: 194.47.206.137 <-- HERE

# Jump server
js_hostname: "ACME_JumpServer"
js_flavor: "c1-r05-d10"
js_sg: "SSHExt"
js_zone: "Education"
js_fixed_ip: 192.168.0.11
js_public: 194.47.206.46 <-- HERE
```
5. Run ``` ansible-playbook site.yml --ask-vault-pass (or --vault-password-file FILE)```

#### Destroy and recreate
In-order to recreate the system, all you need to do is to destroy the instances (And network if you want to) is to run these commands below, depending on your intention.

Destroy instances:
```ansible-playbook destroy_instances.yml```

Destroy network:
```ansible-playbook destroy_network.yml```

Recreate & provision (Both network, if needed and instances)  
```ansible-playbook site.yml --ask-vault-pass (or --vault-password-file FILE)```

# How to run
## Create network & instances
```ansible-playbook network.yml```
## Configure instances
```ansible-playbook site.yml --ask-vault-pass```

or

```ansible-playbook site.yml --vault-password-file <vaultpwdfile>```
### Configure specific instance

```ansible-playbook site.yml --limit INSTANCE-NAME --ask-vault-pass```

or

```ansible-playbook site.yml --limit INSTANCE-NAME --vault-password-file <vaultpwdfile>```
## Destroy instances
```ansible-playbook destroy_instances.yml```
## Destroy network
```ansible-playbook destroy_network.yml```

# How to test
Instances need to be up and provisioned before the tests can be run

```ansible-playbook test/main.yml --ask-vault-pass```

or

```ansible-playbook test/main.yml --vault-password-file <vaultpwdfile>```

# How to backup
Backup of database and wordpress folder run every hour and is saved in the wp instances /tmp/ folder.

Backup server fetches these files and stores them in the /tmp/ folder.

To save the backed up files locally, run
```ansible-playbook get-backup.yml``` and they will be store in the current directory/host_backup
