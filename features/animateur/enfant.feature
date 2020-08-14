Feature: Gestion de sa fiche animateur
  Je suis connecté
  Je fais une recherche
  Je consulte un enfant

  Background:
    Given I am login with user "kevin@marche.be" and password "homer"
    Given I am on "/animateur/enfant/"

  Scenario:  Je fais une recherche
    Then I should see "FERNANDEL Yves"
    Then I should see "PERET Merlin"
    Then I should see "SIMPSON Bart"
    Then I fill in "search_enfant_for_animateur[nom]" with "Yves"
    Then I select "19-09-2024" from "search_enfant_for_animateur_jour"
    And I press "Rechercher"
    Then I should see "FERNANDEL Yves"
    Then I should not see "PERET Merlin"
    Then I should not see "SIMPSON Bart"

  Scenario:  Je consulte un enfant
    Then I follow "SIMPSON Bart"
    Then I should see "Scolarisé à Champlon en 2M"
    Then I follow "Fiche santé"
    Then the response status code should be 200
