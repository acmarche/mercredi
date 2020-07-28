Feature: Test de la gestion des accueils
  J' ajoute un accueil pour un enfant qui n'a pas de fiche santé
  J' ajoute un accueil pour un enfant qui a un fiche santé
  Je ne peux pas supprimer un accueil passée

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: Je visionne un accueil
    Given I am on "/parent/enfant/"
    Then I follow "SIMPSON Bart"
    Then I should see "jeudi 9 juillet 2020"
    Then I follow "jeudi 9 juillet 2020"
    Then I should see "Accueil de SIMPSON Bart le jeudi 9 juillet 2020"
    And I should see "0,50 €"

  Scenario: J' ajoute un accueil pour un enfant qui n'a pas de fiche santé
    When I follow "Accueil matin/soir"
    Then I follow "SIMPSON Lisa"
    Then I should see "La fiche santé de votre enfant doit être complétée"

  Scenario: J' ajoute un accueil pour un enfant qui a un fiche santé
    When I follow "Accueil matin/soir"
    Then I follow "SIMPSON Bart"
    And I fill in "accueil_parent[date_jour]" with "2020-08-09"
    And I fill in "accueil_parent[remarque]" with "papy vient le chercher"
    And I check "Matin"
    And I press "Sauvegarder"
    Then I should see "L'acceuil a bien été ajouté"
    Then I should see "papy vient le chercher"

  Scenario: Je ne peux pas supprimer un accueil passée
    Then I follow "SIMPSON Bart"
    Then I should see "jeudi 9 juillet 2020"
    Then I follow "jeudi 9 juillet 2020"
    Then I should see "Accueil de SIMPSON Bart le jeudi 9 juillet 2020"
    Then I press "Supprimer l'accueil"
    Then I should see "Un accueil passé ne peut être supprimé"
