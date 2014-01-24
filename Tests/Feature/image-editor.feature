Feature: Image Editor
  In order to ensure the Image Editor works as expected
  As a BE User
  I want to have the following scenario succeeding

  @javascript @load
  Scenario: Load the BE module
    Given I click on media module
    Then I should have "200" status code
