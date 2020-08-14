Feature: Gestion des enfants

  Background:
    Given I am login with user "joseph@marche.be" and password "homer"
    Given I am on "/ecole/enfant/"

  Scenario: Je vois la liste des enfants
    Then I should see "Liste des enfants"
    Then I should see "FERNANDEL Yves"
    Then I should see "SIMPSON Bart"

  Scenario: Je consulte la fiche santé
    Then I should see "Liste des enfants"
    Then I follow "SIMPSON Bart"
    Then I follow "Fiche santé"
    And the response status code should be 200
