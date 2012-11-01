Feature: media deletion
  In order to manage my Media collection
  As a User
  I want to delete media
  BEWARE: don't use this feature test with production data!!

  @javascript @delete @media
  Scenario: delete a media
    Given I am on the main page
    And I wait "2" seconds
    And I should see "Delete"
    When I follow "Delete"
    And I wait "1" seconds
    And I follow "OK"
    And I wait "3" seconds
    Then I should see "Media has been deleted"

