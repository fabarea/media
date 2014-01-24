Feature: Media Backend Module
  In order to ensure the Media module works as expected
  As a BE User
  I want to have the following scenario succeeding
  Notice: to skip the login step, the auto-login is provided by an extension such as "cc_iplogin_be"

  @javascript @load
  Scenario: Load the Media module
    Given I click the Media icon
    Then I should get a "200" status code
    And I should see the Grid

  @javascript @upload
  Scenario: Upload a new media
    Given I see the Grid
    And I click on the upload button
    And I select a file in the dialog window
    And I press ok
    Then I should see a new File in the Grid

  @javascript @upload-create-variant
  Scenario: Create Variant upon upload

  @javascript @upload-create-category
  Scenario: Add Category upon upload

  @javascript @edit
  Scenario: Edit a media
    Given I see the Grid
    And I click the "edit" icon within a row
    Then I should be in the edit view

  @javascript @inline-editing
  Scenario: Edit a media in-line
    Given I see the Grid
    And I click on a string that has been configured as editable field like for "title"
    And the string becomes an input field
    And I edit the content
    And I press enter
    Then I must see the updated content

  @javascript @delete
  Scenario: Delete a media
    Given I see the Grid
    And I click the "delete" icon within a row
    And I confirm
    Then I should see "Media has been deleted"

  @javascript @mass-delete
  Scenario: Mass delete media

  @javascript @display-categories
  Scenario: Display categories of a File in the Grid

  @javascript @display-usage
  Scenario: Display usage of a File in the Grid

  @javascript @display-thumbnail
  Scenario: Display a thumbnail of a File in the Grid

  @javascript @display-permission
  Scenario: Display Frontend Permission of a File in the Grid

  @javascript @move-storage
  Scenario: Move File between storage

  @javascript @add-category
  Scenario: Add categories to a File in the Grid

  @javascript @filter
  Scenario: Filter File

  @javascript @filter-metadata
  Scenario: Filter File by metadata