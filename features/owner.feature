Feature: members management

    Background: no members
        Given the database is clean

    Scenario: Card without member assigned
        And exists one card
        When I go to "/card/1"
        Then the response should contain "never assigned"

    Scenario: Card with member assigned
        And exists member "sensorario"
        And exists one card assigned to "sensorario"
        When I go to "/card/1"
        Then the response should contain "sensorario"

    Scenario: Card owner in kanban
        And exists member "sensorario"
        And exists status "todo"
        And exists one card assigned to "sensorario"
        When I go to "/card/kanban"
        Then the response should contain "@sensorario"
