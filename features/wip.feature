Feature: wip limit

    Background: clean database
        Given the database is clean
        And exists a board with wip limit of 42
        And exists admin user
        And exists status "todo" with wip limit 1

        Given I go to "/login"
        Then I fill in "_username" with "admin"
        And I fill in "_password" with "password"
        And I press "login"

    Scenario: Admin try to create a card but board limit is reached
        Given the board have limit 1
        And exists one card with status "todo" assigned to "admin"
        When I go to "/card/new"
        Then the response should contain "wip board limit reached"

    Scenario: Admin try to create a card but board limit is not reached
        Given the board have limit 2
        And exists one card with status "todo" assigned to "admin"
        When I go to "/card/new"
        Then the response should not contain "wip board limit reached"

    Scenario: Admin try to create a card but column limit is reached
        Given the board have limit 2
        And exists one card with status "todo" assigned to "admin"
        When I go to "/card/new"
        And I fill in "Title" with "sample"
        And I fill in "Description" with "sample"
        And I select "1" from "appbundle_card[status]"
        And I press "Create"
        Then the response should not contain "wip board limit reached"
        Then the response should contain "wip column limit reached"
