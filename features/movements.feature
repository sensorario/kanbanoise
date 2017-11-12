Feature: back and forth card movement

    Background: clean database
        Given the database is clean
        And exists a board with wip limit of 42
        And exists admin user
        And exists status "todo"
        And exists status "in progress"
        And exists status "done"

        Given I go to "/login"
        Then I fill in "_username" with "admin"
        And I fill in "_password" with "password"
        And I press "login"

    Scenario: Admin cant move card back when in first column
        Given exists one card with status "todo" assigned to "admin"
        And I go to "/card/kanban"
        And class "back_movement" is not present

    Scenario: Admin cant move card forth when in last column
        Given exists one card with status "done" assigned to "admin"
        And I go to "/card/kanban"
        And class "forth_movement" is not present

    Scenario: Admin can move card back when not in first column
        Given exists one card with status "in progress" assigned to "admin"
        And I go to "/card/kanban"
        And class "back_movement" is present

    Scenario: Admin can move card forth when not in last column
        Given exists one card with status "in progress" assigned to "admin"
        And I go to "/card/kanban"
        And class "forth_movement" is present

    Scenario: Admin move card back when not in first column
        Given exists one card with status "in progress" assigned to "admin"
        And I go to "/card/kanban"
        And I follow "back_movement_link"
        Then there should be one card in "todo" status

    Scenario: Admin move card forth when not in last column
        Given exists one card with status "todo" assigned to "admin"
        And I go to "/card/kanban"
        And I follow "forth_movement_link"
        Then there should be one card in "in progress" status

    Scenario: Admin cant move card back when back column is full
        Given exists one card with status "in progress" assigned to "admin"
        Given exists one card with status "todo" assigned to "admin"
        And column "todo" have wip limit 1
        And I go to "/card/kanban"
        And I follow "back_movement_link"
        And I go to "/card/kanban"
        And the response should contain "wip column limit reached"
