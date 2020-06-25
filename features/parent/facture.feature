Feature: Gestion des factures
  Je suis connecté
  Je consulte une facture

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/tuteur/"
    Then I should see "SIMPSON Homer"

  Scenario: Je consulte une facture
    Then I follow "Mes factures"
    Then I should see "Liste de vos factures"
    Then I should see "Mardi 6 Octobre 2020"

