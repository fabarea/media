Feature: File Picker
  In order to ensure a resource can be picked in the BE
  As a BE User
  I want to have the following scenario succeeding

  @javascript @pick-one
  Scenario: Pick one file
    Given I click on "add Image"
    And a pop-up window open
    And I see the file resources
    And I click add
    Then the image must appear in the parent window

  @javascript @pick-many
  Scenario: Pick many file
