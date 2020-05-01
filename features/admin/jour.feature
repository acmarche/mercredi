Feature: Gestion des écoles
  Je suis connecté
  J' ajoute l'école "Springfield"
  J' édite l'école
  Je supprime l'école de Waha
  Je ne peux pas supprimer l'école de Aye

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/jour/"
    Then I should see "Liste des jours de garde"

  Scenario: Ajout un jour de garde
    Then I follow "Ajouter une date"
    And I fill in "jour[date_jour]" with "2020-05-01"
    And I fill in "jour[prix1]" with "4"
    And I fill in "jour[prix2]" with "3"
    And I fill in "jour[prix3]" with "2"
    And I press "Sauvegarder"
    Then I should see "Springfield"
    Then I should see "Rue des Armoiries"

  Scenario: Modifier un jour de garde
    Then I follow "Aye"
    Then I follow "Editer"
    And I fill in "jour[telephone]" with "084 55 66 99"
    And I press "Sauvegarder"
    Then I should see "084 55 66 99"

  Scenario: Supprimer une école
    Then I follow "Waha"
    Then I press "Supprimer l'école"
   # Then print last response
    Then I should see "L'école a bien été supprimée"

  Scenario: Supprimer une école avec enfants
    Then I follow "Aye"
    Then I press "Supprimer l'école"
    Then I should see "L'école contient des enfants et ne peut être supprimée"
