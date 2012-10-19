Feature: Hello World Feature
  In order to ensure the basic feature of the application
  As a User
  I want to make sure I can see and access the BE module
  Notice: to skip the login step, the auto-login is provided by an extension such as "cc_iplogin_be"

  @javascript @base
  Scenario: module is reachable
    Given I am on the main page
      And I wait "5" seconds
    Then I should see "records per page"
