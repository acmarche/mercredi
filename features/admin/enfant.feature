Feature: Gestion des enfants
  Je suis connecté
  J' ajoute un enfant
  J' édite un enfant
  Je supprime un enfant sans tuteur
  Je supprime un enfant avec tuteur

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des tuteurs"

  Scenario: Ajout une enfant
    Then I follow "Peret"
    Then I follow "ajouter un nouvel enfant"
    Then I should see "Nouvel enfant"
    And I fill in "enfant[nom]" with "Funes"
    And I fill in "enfant[prenom]" with "Jules"
    And I fill in "enfant[birthday]" with "2015-12-06"
    And I select "Waha" from "enfant[ecole]"
    And I select "3M" from "enfant[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "FUNES Jules"
    Then I should see "Waha"

  Scenario: Modifier une enfant
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I follow "Peret"
    Then I follow "Editer"
    And I fill in "enfant[numero_national]" with "781004199"
    And I press "Sauvegarder"
    Then I should see "781004199"

  Scenario: Supprimer un enfant sans tuteur
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I follow "Gauthie"
    Then I press "Supprimer l'enfant"
   # Then print last response
    Then I should see "L'enfant a bien été supprimé"

  Scenario: Supprimer un enfant avec tuteur
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I follow "Peret"
    Then I press "Supprimer l'enfant"
    Then I should see "L'enfant a bien été supprimé"