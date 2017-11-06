Feature: members management

    Background: no members
        Given the database is clean
        And exists a board with wip limit of 2
        And exists admin user
        And exists status todo

    Scenario: User can create member

        Given I go to "/login"
        Then I fill in "_username" with "admin"
        And I fill in "_password" with "password"
        And I press "login"

        And I go to "/member/new"
        When I fill in "Name" with "sensorario"
        And I press "Create"
        Then the response should contain "sensorario created"

