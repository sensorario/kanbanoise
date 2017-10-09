Feature: user greetings

    Scenario: Guest open kanbanoise and see greetings
        Given go to "/"
        Then the response should contain "Welcome to Kanbanoise"
