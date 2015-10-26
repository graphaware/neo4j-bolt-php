Feature: Basic Result Metadata API

  Scenario: Summarize Result
    Given there is a driver configured with the "localhost" uri
    When I run a statement
    And I summarize it
    Then I should get a Result Summary back

  Scenario: Access Statement
    Given there is a driver configured with the "localhost" uri
    When I run a statement
    And I summarize it
    And I request a statement from it
    Then I should get a Statement back