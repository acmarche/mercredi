Feature: Gestion des factures
  Je suis connecté
  Je consulte une facture
  J'envoie une facture
  Je modifie une facture
  Je supprime une facture
  Je crée une facture
  J'attache des présences
  Je cherche une facture

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des tuteurs"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I should see "Simpson"

  Scenario: Je consulte une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I should see "mercredi 6 mai 2020"
    Then I should see "25 €"

  Scenario: J'envoie une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Envoyer par mail"
    And I fill in "facture_send[sujet]" with "Voici votre facture"
    And I fill in "facture_send[texte]" with "Payer sur le compte x"
    And I press "Envoyer la facture"
    Then I should see "La facture a bien été envoyée"

  Scenario: Je modifie une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Modifier"
    And I fill in "facture_edit[code_postal]" with "6980"
    And I press "Sauvegarder"
    Then I should see "La facture a bien été modifiée"
    Then I should see "6980"

  Scenario: Je supprime une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    And I press "Supprimer la facture"
    Then I should see "La facture a bien été supprimée"

  Scenario: Je crée une facture
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I follow "Créer une facture"
    Then I should see "Mercredi 2 septembre 2020"
    Then I should not see "Mardi 6 Octobre 2020"
    And I fill in "facture[remarque]" with "Vous avez 3 jours pour payer"
    And I press "Générer la facture"
    Then I should see "La facture a bien été crée"
    Then I should see "Simpson Lisa"
    Then I should see "mercredi 2 septembre 2020"

  Scenario: Je détache une présence
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Détails"
    Then I should see "Simpson Bart"
    Then I should see "25 €"
    And I press "Détacher la présence"
    Then I should see "La présence a bien été détachée"

  Scenario: J'attache des présences
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Attacher des présences"
    Then I should see "Mercredi 2 septembre 2020"
    And I press "Attacher à la facture"
    Then I should see "Les présences ont bien été attachées"

  Scenario: Je cherche une facture
    Given I am on "/admin/facture/search"
    And I fill in "facture_search[tuteur]" with "simps"
    And I select "Non payée" from "facture_search[paye]"
    And I press "Rechercher"
    Then I should see "Mardi 6 Octobre 2020"
    Then I should see "SIMPSON Homer"
