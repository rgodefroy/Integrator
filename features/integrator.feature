Feature: Manage integration scripts
    In order to validate software evolutions
    As a developer 
    I need to know if they work on integration plateform
    
    Scenario: Successfully listing projects last run status
        Given I am on the homepage
        Then I should see list of projects in projects folder
        And it should display name and status of last actions ran
        
        | project                 | last action status
        | projet1                 | post-commit, 2013-06-06 14:12:35 done
        | projet2                 | post-commit, 2013-06-08 14:00:35 done
        
    Scenario: Successfully listing actions executions history
        Given I am on the homepage
        And I click on history
        Then I should see list of last 100 action executions
        And it should display project, action, date, status

    Scenario: Successfully getting details about a project
        Given there is a project with name "test"
        And I am on the the project list page
        When I follow "view" in the list for "test"
        Then I should be on "test" project details page
        And it should display id, name , nodes , status

    Scenario: Successfully running test after a commit
        Given there is a project with name "test-project"
        And a commit has been launched for "test-project"
        And there is a post-commit hook
        Then action "post-commit" for "test-project" should be executed
        And "developers" should be notified by email
        
    Scenario: Successfully consulting results of a test        
        Given there is a project with name "test"
        And action "post-commit" for "test" has been executed
        And I am on the homepage
        When I follow "view" in the project list for "test"
                
  Scenario: Successfully running test manualy
  
  Scenario : Successfully run new execution of a action 
  
  Scenario : Successfully launch a action in command line 
  
  Scenario : Failing to run new execution of a action currently running 

    
