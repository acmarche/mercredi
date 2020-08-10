Feature: Gestion des utilisateurs
  Je suis connecté
  Je modifie un utilisateur
  Je modifie les rôles d'un utilisateur
  J'associe un parent sans les bons droits
  J'associe un parent avec les bons droits
  J'associe une école avec les bons droits
  Je change le mot de passe et je me connecte avec le nouveau mot de passe
  Je supprime un utilisateur
  Je recherche l'utilisateur jf qui est admin

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I should see "Liste des utilisateurs"

  Scenario: Ajout un utilisateur administrateur
    When I follow "Ajouter un utilisateur"
    And I fill in "user[nom]" with "Botteman"
    And I fill in "user[prenom]" with "Bob"
    And I fill in "user[email]" with "bob@mail.com"
    And I fill in "user[plainPassword]" with "homer123"
    And I check "Administrateur"
    And I press "Sauvegarder"
    Then I should see "bob@mail.com"
    Then I should see "Botteman"
    Then I should see "ROLE_MERCREDI_ADMIN"

  Scenario: Modifier un utilisateur
    When I follow "Cohen Leonard"
    Then I follow "Modifier"
    And I fill in "user_edit[nom]" with "De Vinci"
    And I press "Sauvegarder"
    Then I should see "DE VINCI Leonard"

  Scenario: Je modifie les rôles d'un utilisateur
    When I follow "Cohen Leonard"
    Then I follow "Rôles"
    And I check "Ecole"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur a bien été modifié"
    Then I should see "ROLE_MERCREDI_ECOLE"

  Scenario: J'associe un parent sans les bons droits
    When I follow "Cohen Leonard"
    Then I follow "Associer un parent"
    Then I should see "Le compte n'a pas le rôle de parent"

  Scenario: J'associe un parent avec les bons droits
    When I follow "Cohen Albert"
    Then I follow "Associer un parent"
    And I select "GASPARD Aurore" from "associate_parent_tuteur"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "L'utilisateur a bien été associé."
    Then I should see "Un mail de bienvenue a été envoyé"
    Then I should see "GASPARD Aurore"

  Scenario: J'associe une école avec les bons droits
    When I follow "Cohen Albert"
    Then I follow "Associer une école"
    And I select "Aye" from "associate_ecole[ecoles]"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "L'utilisateur a bien été associé."
    Then I should see "Aye"

  Scenario: Je change le mot de passe et je me connecte avec le nouveau mot de passe
    When I follow "Cohen Albert"
    Then I follow "Changer le mot de passe"
    And I fill in "user_password[plainPassword][first]" with "lisa"
    And I fill in "user_password[plainPassword][second]" with "lisa"
    And I press "Sauvegarder"
    Then I should see "Le mot de passe a bien été modifié"
    Given I am on "/logout"
    When I am login with user "albert@marche.be" and password "lisa"
    Then I should see "Accès en tant que parent"
    Then I follow "Accès en tant que parent"
    Then I should see "Vos coordonnées"

  Scenario: Supprimer une école
    When I follow "Cohen Albert"
    Then I press "Supprimer l'utilisateur"
   # Then print last response
    Then I should see "L'utilisateur a bien été supprimé"

  Scenario: Je recherche l'utilisateur jf qui est admin
    When I fill in "user_search[nom]" with "Jf"
    And I select "Administrateur" from "user_search_role"
    And I press "Rechercher"
    Then I should see "Simpson Jf"
    Then I should not see "Cohen Albert"
