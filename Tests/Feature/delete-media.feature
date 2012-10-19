Feature: log-in form
  In order to access restricted content
  As a User
  I want to make sure that the log-in behaves correctly

  @javascript @create @media
  Scenario: log-in
    Given I am logged in as "itest" with password "itest"
    Then I should see "Vous trouvez de l'information du domaine privé"


  @javascript
  Scenario: log-out
    Given I am logged in as "itest" with password "itest"
      And I am on "http://fr.wycliffe.ch/service/login/"
    When I press "Déconnexion"
    Then I should see "Wycliffe Suisse"
