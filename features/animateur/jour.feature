Feature: Gestion de sa fiche animateur
  Je suis connecté
  Je fais une recherche

  Background:
    Given I am login with user "kevin@marche.be" and password "homer"
    Given I am on "/animateur/presence/"

  Scenario:  Page index
    Then I should see "Mes jours de travail"
    Then I should see "mercredi 6 mai 2020"
    Then I should see "jeudi 19 septembre 2024"

  Scenario:  Je consulte une date
    Then I follow "mercredi 6 mai 2020"
    Then I should see "SIMPSON Bart"
    Then I should not see "PERET Merlin"
