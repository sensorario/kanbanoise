Feature: members management

    Background: no members
        Given the database is clean

    Scenario: User can create member
        And I go to "/member/new"
        When I fill in "Name" with "sensorario"
        And I press "Create"
        Then the response should contain "sensorario created"

    Scenario: User can create member
        And I go to "/member/new"
        When I fill in "Name" with "sensorario"
        And I press "Create"
        When I reload the page
        Then the response should not contain "sensorario created"
