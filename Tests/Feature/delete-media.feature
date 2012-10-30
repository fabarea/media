Feature: log-in form
  In order to access restricted content
  As a User
  I want to make sure that the log-in behaves correctly
  BEWARE: don't use production data!!

  @javascript @delete @media
  Scenario: delete a media
    Given I am on the main page
    And I wait "2" seconds
    And I should see "Delete"
    When I follow "Delete"
    And I wait "1" seconds
    And I follow "OK"
    And I wait "1" seconds
    Then I should see "has been deleted"

