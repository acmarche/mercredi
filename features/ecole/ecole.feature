Feature: Gestion des écoles
  Je suis connecté
  Je vois les enfants d'une école sur la page de détail
  J' édite l'école
  Je supprime l'école de Waha
  Je ne peux pas supprimer l'école de Aye
  Enfants de l'école

  Background:
    Given I am login with user "joseph@marche.be" and password "homer"
    Given I am on "/ecole/ecole"

  Scenario: Je vois les enfants d'une école sur la page de détail
    Then I should see "Liste de vos écoles"
    Then I should see "Champlon"
    Then I follow "Champlon"
    Then I should see "Yves"
    Then I should see "Bart"
