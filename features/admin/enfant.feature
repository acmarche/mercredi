Feature: Gestion des enfants
  Je suis connecté
  J' ajoute un enfant
  J' édite un enfant
  Je supprime un enfant sans tuteur
  Je supprime un enfant avec tuteur

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"

  Scenario: Ajout une enfant
    Then I follow "Ajouter un enfant"
    And I fill in "enfant[nom]" with "Springfield"
    And I fill in "enfant[rue]" with "Rue des Armoiries"
    And I fill in "enfant[code_postal]" with "6900"
    And I fill in "enfant[localite]" with "Hargimont"
    And I press "Sauvegarder"
    Then I should see "Springfield"
    Then I should see "Rue des Armoiries"

  Scenario: Modifier une enfant
    Then I follow "Aye"
    Then I follow "Editer"
    And I fill in "enfant[telephone]" with "084 55 66 99"
    And I press "Sauvegarder"
    Then I should see "084 55 66 99"

  Scenario: Supprimer un enfant sans tuteur
    Then I follow "Waha"
    Then I press "Supprimer l'enfant"
   # Then print last response
    Then I should see "L'enfant a bien été supprimée"

  Scenario: Supprimer un enfant avec tuteur
    Then I follow "Aye"
    Then I press "Supprimer l'enfant"
    Then I should see "L'enfant contient des enfants et ne peut être supprimée"
