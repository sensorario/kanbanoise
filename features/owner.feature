Feature: members management

    Background: no members
        Given the database is clean

    Scenario: Card without member assigned
        And exists one card
        When I go to "/card/1"
        Then the response should contain "not yet assigned"
