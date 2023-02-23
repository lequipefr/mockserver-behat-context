Feature: Reset

    Scenario: Reset all expectations.
        Mockerver is already reset before all scenarios though.

        Given I reset mocks

        Then mockserver should have been reset
