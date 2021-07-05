Feature: Test des pages ecoles
  Je suis sur la page d'accueil
  Je vois l'école de Aye

  Background:
    Given I am login with user "joseph@marche.be" and password "homer"
    Given I am on "/ecole"

  Scenario: Je suis sur la page d'accueil
    Then I should see "GAUTHIE"
    Then I should see "Aye"

