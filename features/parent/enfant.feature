Feature: Test des pages parents
  Je suis sur la page d'accueil
  J'affiche un enfant
  J' ajoute un enfant
  En vue détail, Lisa n'a pas sa fiche santé complète
  En vue détail, Bart a sa fiche santé complète
  J'affiche une attestation

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent/enfant/"
    Then I should see "Liste de mes enfants"

  Scenario: J'affiche un enfant
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"

  Scenario: J' ajoute un enfant
    Then I follow "Ajouter un enfant"
    Then I should see "Nouvel enfant"
    And I fill in "enfant_edit_for_parent[nom]" with "Funes"
    And I fill in "enfant_edit_for_parent[prenom]" with "Jules"
    And I fill in "enfant_edit_for_parent[birthday]" with "2015-12-06"
    And I select "Waha" from "enfant_edit_for_parent[ecole]"
    And I select "Masculin" from "enfant_edit_for_parent[sexe]"
    And I select "3M" from "enfant_edit_for_parent[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "FUNES Jules"
    Then I should see "Waha"

  Scenario: Je modifie un enfant sans fiche santé
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"
    Then I follow "Modifier"
    And I select "Waha" from "enfant_edit_for_parent[ecole]"
    And I select "3M" from "enfant_edit_for_parent[annee_scolaire]"
    And I press "Sauvegarder"
    Then I should see "L'enfant a bien été modifié"
    Then I should see "Waha"
    Then I should see "3M"

  Scenario: Je modifie sa fiche santé
    Then I follow "SIMPSON Lisa"
    Then I should see "SIMPSON Lisa"
    Then I follow "Fiche santé"
    Then I should see "Fiche santé de SIMPSON Lisa"
    And I fill in "sante_fiche[medecin_nom]" with "Ledoux"
    And I fill in "sante_fiche[medecin_telephone]" with "084 32 55 66"
    And I fill in "sante_fiche[personne_urgence]" with "Papa et maman"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    Then I should see "084 32 55 66"
    Then I should see "Papa et maman"
    Then I should see "Ledoux"

  Scenario: Lisa n'a pas sa fiche santé complète
    Then I follow "SIMPSON Lisa"
    Then I should see "Attention la fiche santé n'est pas complète, veuillez la remplir."

  Scenario: Bart a sa fiche santé complète
    Then I follow "SIMPSON Bart"
    Then I should not see "Attention la fiche santé n'est pas complète, veuillez la remplir."

  Scenario: J'affiche une attestation fiscale
    Then I follow "Télécharger 2020"
    And the response status code should be 200
