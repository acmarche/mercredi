Feature: Gestion des présences
  Ajouter une présence avec un tuteur
  Ajouter une présence sans tuteur
  Ajouter une présence avec 2 tuteurs
  Editer une présence
  Supprimer une présence

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"

  Scenario: Ajout une présence avec tuteur
    Then I follow "Peret"
    Then I follow "Ajouter une présence"
    Then I should see "Nouvelle présence pour PERET Merlin"
    And I select "02-09-2020" from "presence_new_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"
    Then I should see "Mercredi 2 septembre 2020"

  Scenario: Ajout une présence 2 tuteurs
    Then I follow "Fernandel"
    Then I follow "Sous la garde de GASPARD Aurore"
    Then I should see "Nouvelle présence pour FERNANDEL Yves"
    When I select "02-09-2020" from "presence_new_jours"
    When I additionally select "16-09-2020" from "presence_new_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"
    Then I should see "Mercredi 2 septembre 2020"

  Scenario: J'édite une présence
    Then I follow "Peret"
    Then I follow "Mercredi 16 septembre 2020"
    Then I should see "Détail de la présence de PERET Merlin"
    Then I follow "Editer"
    And I select "Oui avec certificat" from "presence_absent"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été modifiée"
    Then I should see "Oui avec certificat"

  Scenario: Je supprime une présence
    Then I follow "Peret"
    Then I follow "Mercredi 16 septembre 2020"
    Then I should see "Détail de la présence de PERET Merlin"
    Then I press "Supprimer la présence"
    Then I should see "La présence a bien été supprimée"
