Feature: Gestion des fiches santés
  Je suis connecté
  J' ajoute une fiche a un enfant
  J' édite une fiche

  Background:
    Given I am logged in as an admin

  Scenario: Nouvelle fiche santé
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Fernandel"
    And I press "Rechercher"
    Then I should see "Fernandel"
    Then I follow "Fernandel"
    Then I follow "Fiche santé"
    Then I should see "Cette enfant n'a pas encore de fiche santé"
    And I fill in "sante_fiche[medecin_nom]" with "Ledoux"
    And I fill in "sante_fiche[medecin_telephone]" with "084 32 55 66"
    And I fill in "sante_fiche[personne_urgence]" with "Papa et maman"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
    Then I should see "084 32 55 66"
    Then I should see "Papa et maman"
    Then I should see "Ledoux"

  Scenario: Modifier une fiche
    Given I am on "/admin/enfant/"
    Then I should see "Liste des enfants"
    Then I fill in "search_enfant[nom]" with "Peret"
    And I press "Rechercher"
    Then I should see "Peret"
    Then I follow "Peret"
    Then I follow "Fiche santé"
    Then I follow "Modifier"
    And I fill in "sante_fiche[medecin_nom]" with "Docteur maboulle"
   # And I follow "Ajouter un accompagnateur"
   # Then print last response
   # And I fill in "sante_fiche[accompagnateurs][0]" with "La grande soeur"
    And I press "Sauvegarder"
    Then I should see "Le formulaire santé a bien été enregistré"
   # Then I should see "La grande soeur"
    Then I should see "Docteur maboulle"
