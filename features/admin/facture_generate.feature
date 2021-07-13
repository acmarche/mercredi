Feature: Gestion des factures
  Je suis connecté
  Je génère pour tous pour un mois
  Je génère par mois pour un tuteur
  Je génère manuellement pour un tuteur
  Je modifie une facture
  Je supprime une facture
  Je détache une présence
  J'attache des présences
  Je détache un accueil
  J'attache des accueils

  Background:
    Given I am logged in as an admin

  Scenario: Je génère pour tous pour un mois
    Given I am on "/admin/facture/for/all/"
    Then I should see "Générer les factures"
    Then I fill in "facture_select_month[mois]" with "09-2024"
    And I press "Générer les factures"
    Then I should see "Les 4 factures ont bien été crées"
    Then I fill in "facture_search[mois]" with "09-2024"
    And I press "Rechercher"
    Then I should see "FERNANDEL Franz"
    Then I should see "GASPARD Aurore"
    Then I should see "FERNANDEL Franz"

  Scenario: Je génère par mois pour un tuteur
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    And I fill in "facture_select_month[mois]" with "09-2024"
    And I press "Créer la facture"
    Then I should see "La facture a bien été crée"
    Then I should see "SIMPSON Homer"
    Then I should see "Simpson Lisa"
    Then I should see "mercredi 4 septembre 2024"
    Then I should see "09-2024"

  Scenario: Je génère manuellement pour un tuteur
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I follow "Manuellement"
    Then I should see "Mercredi 4 septembre 2024"
    Then I should see "Lundi 16 décembre 2024"
    Then I should see "Jeudi 9 juillet 2020"
    Then I should not see "Mercredi 6 mai 2020"
    Then I should not see "Mercredi 9 décembre 2020"
    And I fill in "facture[mois]" with "09-2024"
    And I press "Générer la facture"
    Then I should see "La facture a bien été crée"
    Then I should see "Simpson Lisa"
    Then I should see "mercredi 4 septembre 2024"
    Then I should see "lundi 16 décembre 2024"
    Then I should see "jeudi 9 juillet 2020"

  Scenario: Je modifie une facture
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
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
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    And I press "Supprimer la facture"
    Then I should see "La facture a bien été supprimée"

  Scenario: Je détache une présence
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "mercredi 6 mai 2020"
    Then I should see "Simpson Bart"
    Then I should see "25 €"
    And I press "Détacher la présence"
    Then I should see "La présence a bien été détachée"

  Scenario: J'attache des présences
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Attacher des présences"
    Then I should see "Mercredi 4 septembre 2024"
    And I press "Attacher à la facture"
    Then I should see "Les présences ont bien été attachées"

  Scenario: Je détache un accueil
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "mercredi 9 décembre 2020"
    Then I should see "Simpson Bart"
    Then I should see "0.5 €"
    And I press "Détacher l'accueil"
    Then I should see "L'accueil a bien été détaché"

  Scenario: J'attache des accueils
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des parents"
    Then I fill in "search_tuteur[nom]" with "Simpson"
    And I press "Rechercher"
    Then I follow "Simpson"
    Then I follow "Ses factures"
    Then I should see "Mardi 6 Octobre 2020"
    Then I follow "Mardi 6 Octobre 2020"
    Then I follow "Attacher des accueils"
    Then I should see "Jeudi 9 juillet 2020"
    Then I should see "Lundi 21 septembre 2020"
    And I press "Attacher à la facture"
    Then I should see "Les accueils ont bien été attachés"
