Feature: Media edition
  In order to manage my Media collection
  As a User
  I want to edit a media which encompases meta-data, ...
  BEWARE: don't use this feature test with production data!!

  @javascript @edit @media
  Scenario: User can edit a media
    Given I am on the main page
    And I wait "2" seconds
    And I should see "Delete"
    When I follow "Edit"
    And I wait "2" seconds
    And I fill in the following:
        | Title             | Hello             |
        | Description       | World             |
        | Keywords          | Foo               |
        | Source            | Bar               |
    And I press "Save"
    And I wait "3" seconds
    Then I should see "Media updated with success"
