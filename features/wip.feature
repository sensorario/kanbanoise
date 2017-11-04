Feature: wip limit

    Background: clean database
        Given the database is clean
        And exists admin user

        Given I go to "/login"
        Then show last response
        #Then I fill in "_username" with "admin"
        #And I fill in "_password" with "bar"
        #And I press "login"

    #Scenario: cant exceed wip limit
        #Given exists a board with wip limit of 2
        #When exists status "todo" with wip limit 1
        #And exists member "sensorario"
        #And exists one card assigned to "sensorario"
        #And I go to "/card/new"
        #And I fill in "Title" with "sample"
        #And I fill in "Description" with "sample"
        #And I select "todo" from "appbundle_card[status]"
        #And I press "Create"
        #Then the response should contain "wip column limit reached"

    #Scenario: board's wip limit deny card creation
        #Given exists a board with wip limit of 1
        #And exists status "in progress" with wip limit 42
        #And exists status "todo" without wip limit
        #And exists member "sensorario"
        #And exists one card assigned to "sensorario"
        #When I go to "/card/new"
        #Then the response should contain "wip board limit reached"
