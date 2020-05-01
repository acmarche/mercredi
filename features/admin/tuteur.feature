Feature: Gestion des tuteurs
  Je suis connecté
  J' ajoute le tuteur
  J' édite le tuteur
  Je supprime le tuteur sans enfants
  Je supprime le tuteur avec enfants

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/tuteur/"
    Then I should see "Liste des tuteurs"

  Scenario: Ajout un tuteur
    Then I follow "Un tuteur"
    And I fill in "tuteur[nom]" with "Marchal"
    And I fill in "tuteur[prenom]" with "Joseph"
    And I fill in "tuteur[rue]" with "Rue des Dentelles"
    And I fill in "tuteur[code_postal]" with "6900"
    And I fill in "tuteur[localite]" with "Marloie"
    And I fill in "tuteur[telephone]" with "047 58 99 66"
    And I fill in "tuteur[email]" with "joseph@domain.be"
    And I fill in "tuteur[conjoint][nom_conjoint]" with "Mitch"
    And I fill in "tuteur[conjoint][prenom_conjoint]" with "Madeleine"
    And I press "Sauvegarder"
    Then I should see "MARCHAL Joseph"
    Then I should see "Rue des Dentelles"

  Scenario: Modifier un tuteur
    Then I follow "Peret"
    Then I follow "Editer"
    And I fill in "tuteur[gsm]" with "0476 22 66 99"
    And I select "Masculin" from "tuteur_sexe"
    And I press "Sauvegarder"
    Then I should see "0476 22 66 99"

  Scenario: Supprimer un tuteur sans enfant
    Then I follow "Dupont"
    Then I press "Supprimer le tuteur"
   # Then print last response
    Then I should see "Le tuteur a bien été supprimé"

  Scenario: Supprimer un tuteur avec enfants
    Then I follow "Peret"
    Then I press "Supprimer le tuteur"
    Then I should see "Le tuteur a bien été supprimé"
    Given I am on "/admin/checkup/orphelin/"
    Then I should see "Les enfants sans tuteur"
    Then I should see "Merlin"
