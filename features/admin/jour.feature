Feature: Gestion des dates de garde
  Je suis connecté
  J' ajoute une date"
  J' édite une date
  Je supprime une date sans presence
  Je supprime une date avec presence

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/jour/"
    Then I should see "Liste des jours de garde"

  Scenario: Ajout un jour de garde
    Then I follow "Ajouter une date"
    And I fill in "jour[date_jour]" with "2020-05-02"
    And I fill in "jour[prix1]" with "4"
    And I fill in "jour[prix2]" with "3"
    And I fill in "jour[prix3]" with "2"
    And I press "Sauvegarder"
    Then I should see "4 €"
    Then I should see "Samedi 2 mai 2020"

  Scenario: Modifier un jour de garde
    Then I follow "Mercredi 9 Septembre 2020"
    Then I follow "Editer"
    And I fill in "jour[prix1]" with "3.66"
    And I press "Sauvegarder"
    Then I should see "3.66 €"

  Scenario: Supprimer une date sans présence
    Then I follow "Mercredi 9 Septembre 2020"
    Then I press "Supprimer la date"
   # Then print last response
    Then I should see "La date a bien été supprimée"

  Scenario: Supprimer une date avec présence
    Then I follow "Mercredi 9 Septembre 2020"
    Then I press "Supprimer la date"
    Then I should see "La date a bien été supprimée"
