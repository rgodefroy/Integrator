Integrator
==========

Description
------------

Integrator is an continuous integration Silex application 

### Definitions:

Project:    A group of actions linked to deliver a service

Node:       A server or an element of integration sofware plateform

Action:     A group of tasks to execute (post-commit, pre-commit, ...)

Installation
------------

### Requirement:

ssh2-ext

Composer

### Install:

Run composer on the project directory 

composer install --prefer-dist

You need a server compatible with Silex Application (http://silex.sensiolabs.org/doc/web_servers.html) 

Usage
-----

### Configuration

First of all, you need to config your app in folder /config, file config.yml

paths:
  data: Where you want to save execution results      
  projects: Where you put your projects (file.yml) 
  
process: Name of execution process      


* Adding a new project

Then you need to create a new project file (Yaml file) in the /projects folder. The filename  is used as id in the application

Here there is the syntax : (in "actions", node must have the same name as the name in nodes) 


        name: Project's name
        description: Describe the project
		
		notifications:
		email:
        recipients: [sample@sample.fr, sample2@sample.fr]
        nodes:
            Host1:
                host: hostname1
                port: portnumber
                user: usr
                password: password
            Host2:
                host: hostname2
                port: portnumber
                user: user
                password: password

        actions:
            action1:
                task1:
                    node: Host1
                    commands: 
                        - command1
                        - command2
                        - ...
                task2:
                    node: Host2
                    commands: 
                        - command1
                        - command2
                        - ...
            action2:
                task1:
                    node: Host1
                    commands: 
                        - command1
                        - command2
                        - ...
                task2:
                    node: Host2
                    commands: 
                        - command1
                        - command2
                        - ...


* Homepage (Project List)

The project list diplays all projects, with the status of the last execution.

In the column "Last execution', there is the date, the hour, and the name of the last trigger executed

The status tells if the last execution has been successfully executed (done) or not (error). You can click on the link for more details, and to view all steps.


* Running an action

In the homepage, choose your project in the List (just click on the project's name)

Then, choose your action under "Actions" and run it (click on "run").

You can also consult the history for the trigger (link "history" next to "run")

When the execution is finished, the application displays and saves the result in the /data folder (configurable in "config/config.yml").

You can also run a trigger in command line using .bin/integrator.php (useful after svn commit for example)

* History

You can consult the last hundred executions in the history tab. It diplays the result of the execution selected.

Limitations
-----------

yaml multiple lines for command doesn't work (because of ";echo ?$" to get ExitCode) 


