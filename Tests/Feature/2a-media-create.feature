Feature: Media creation
  In order to manage the Media collection
  As a User
  I want to create new media
  BEWARE: don't use this feature test with production data!!

  @javascript @create @media
  Scenario: User can create a new media
    Given I am on the main page
    And I wait "2" seconds
    And I should see "New Media"
    When I follow "New Media"
    And I wait "2" seconds
    And I fill in the following:
      | Title       | Hello |
      | Description | World |
      | Keywords    | Foo   |
      | Source      | Bar   |
    And I press "Save"
    And I wait "3" seconds
    Then I should see "Media created with success"
