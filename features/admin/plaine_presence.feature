Feature: Gestion des présences des plaines
  Je suis connecté
  Ajout une présence avec plusieurs tuteurs
  Ajout une présence avec un tuteur

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"

  Scenario: Ajout une présence avec plusieurs tuteurs
    Then I follow "Plaine de noel"
    And I follow "Inscrire un enfant"
    And I should see "Nouvelle inscription à Plaine de noel"
    And I follow "Fernandel"
    And I follow "FERNANDEL Franz"
    Then I should see "Détails de l'inscription de FERNANDEL Yves à Plaine de noel"
    Then I should see "lundi 21 décembre 2020"
    Then I should see "mardi 22 décembre 2020"
    Then I should see "mercredi 23 décembre 2020"

  Scenario: Ajout une présence avec un tuteur
    Then I follow "Plaine de noel"
    And I follow "Inscrire un enfant"
    And I should see "Nouvelle inscription à Plaine de noel"
    And I follow "Peret"
    Then I should see "Détails de l'inscription de PERET Merlin à Plaine de noel"
    And I should see "lundi 21 décembre 2020"
    And I should see "mardi 22 décembre 2020"
    And I should see "mercredi 23 décembre 2020"

  Scenario: Changer les dates de présence
    Then I follow "Plaine de noel"
    And I follow "Détails"
    And I follow "Changer ses dates"
    And I uncheck "22-12-2020"
    Then I press "Sauvegarder"
    And I should see "Les présences ont bien été modifiées"
    And I should see "lundi 21 décembre 2020"
    And I should not see "mardi 22 décembre 2020"
    And I should see "mercredi 23 décembre 2020"

