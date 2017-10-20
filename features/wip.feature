Feature: wip limit

    Background: clean database
        Given the database is clean

    Scenario: cant exceed wip limit
        When exists status "todo" with wip limit 1
        And exists member "sensorario"
        And exists one card assigned to "sensorario"
        And I go to "/card/new"
        And I fill in "Title" with "sample"
        And I fill in "Description" with "sample"
        And I select "todo" from "appbundle_card[status]"
        And I press "Create"
        Then the response should contain "wip limit reached"
