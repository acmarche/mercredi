Feature: Gestion des écoles
  Je suis connecté
  J' ajoute l'école "Springfield"
  J' édite l'école
  Je supprime l'école de Waha
  Je ne peux pas supprimer l'école de Aye

  Scenario: Ajout une école
    Given I am logged in as an admin
    Given I am on "/admin/ecole/"
    Then I should see "Liste des écoles"
    Then I follow "Ajouter une école"
    And I fill in "ecole[nom]" with "Springfield"
    And I fill in "ecole[rue]" with "Rue des Armoiries"
    And I fill in "ecole[code_postal]" with "6900"
    And I fill in "ecole[localite]" with "Hargimont"
    And I press "Sauvegarder"
    Then I should see "Springfield"
    Then I should see "Rue des Armoiries"

  Scenario: Modifier une école
    Given I am logged in as an admin
    Given I am on "/admin/ecole/"
    Then I should see "Liste des écoles"
    Then I follow "Aye"
    Then I follow "Editer"
    And I fill in "ecole[telephone]" with "084 55 66 99"
    And I press "Sauvegarder"
    Then I should see "084 55 66 99"

  Scenario: Supprimer une école
    Given I am logged in as an admin
    Given I am on "/admin/ecole/"
    Then I should see "Liste des écoles"
    Then I follow "Waha"
    Then I press "Supprimer l'école"
   # Then print last response
    Then I should see "L'école a bien été supprimée"

  Scenario: Supprimer une école avec enfants
    Given I am logged in as an admin
    Given I am on "/admin/ecole/"
    Then I should see "Liste des écoles"
    Then I follow "Aye"
    Then I press "Supprimer l'école"
    Then I should see "L'école contient des enfants et ne peut être supprimée"
