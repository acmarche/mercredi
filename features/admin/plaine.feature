Feature: Gestion des plaines
  Je suis connecté
  J' ajoute l'école "Springfield"
  J' édite l'école
  Je supprime l'école de Waha
  Je ne peux pas supprimer l'école de Aye

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"

  Scenario: Ajout une plaine
    Then I follow "Ajouter une plaine"
    And I fill in "plaine[nom]" with "Carnaval 2020"
    And I fill in "plaine[prix1]" with "8"
    And I fill in "plaine[prix1]" with "5"
    And I fill in "plaine[plaine_groupes][0][inscription_maximum]" with "20"
    And I fill in "plaine[plaine_groupes][1][inscription_maximum]" with "15"
    And I fill in "plaine[plaine_groupes][2][inscription_maximum]" with "12"
    And I press "Sauvegarder"
    Then I should see "Dates pour Carnaval 2020"
    Then I fill in "plaine_jour[jours][0][date_jour]" with "20"
    Then I fill in "plaine_jour[jours][1][date_jour]" with "20"
    And I press "Sauvegarder"
    Then I should see "Rue des Armoiries"

  Scenario: Modifier une plaine
    Then I follow "Aye"
    Then I follow "Modifier"
    And I fill in "ecole[telephone]" with "084 55 66 99"
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
