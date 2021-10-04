Feature: Gestion des factures
  Je suis connecté
  Je ne mets ni forfait ni pourcentage
  J'ajoute un complément forfaitaire
  J'ajoute un complément pourcentage

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I should see "mercredi 6 mai 2020"

  Scenario: Je ne mets ni forfait ni pourcentage
    Then I follow "Ajouter un complément"
    Then I should see "Nouveau complément pour la facture du mardi 6 octobre 2020 de SIMPSON Homer"
    Then I fill in "facture_complement[nom]" with "En retard"
    Then I fill in "facture_reduction[dateLe]" with "2021-10-04"
    And I press "Sauvegarder"
    Then I should see "Vous devez appliquer un pourcentage ou une réduction"

  Scenario: J'ajoute un complément forfaitaire
    Then I follow "Ajouter un complément"
    Then I should see "Nouveau complément pour la facture du mardi 6 octobre 2020 de SIMPSON Homer"
    Then I fill in "facture_complement[nom]" with "Retard"
    Then I fill in "facture_complement[forfait]" with "12"
    Then I fill in "facture_complement[dateLe]" with "2021-10-04"
    And I press "Sauvegarder"
    Then I should see "Retard"
    Then I should see "12,00 €"
    Then I should see "37,50 €"

  Scenario: J'ajoute un complément pourcentage
    Then I follow "Ajouter un complément"
    Then I should see "Nouveau complément pour la facture du mardi 6 octobre 2020 de SIMPSON Homer"
    Then I fill in "facture_complement[nom]" with "Retard"
    Then I fill in "facture_complement[pourcentage]" with "12"
    Then I fill in "facture_complement[dateLe]" with "2021-10-04"
    And I press "Sauvegarder"
    Then I should see "Retard"
    Then I should see "12 %"
    Then I should see "3,06 €"
    Then I should see "28,56 €"

