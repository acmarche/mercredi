Feature: Test des pages parents
  Je suis sur la page d'accueil
  Fiche santé complète pour bart mais pas pour lisa

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: Je suis sur la page d'accueil
    Then I should see "Votre (Vos) enfant(s)"
    Then I should see "SIMPSON Lisa"
    Then I should see "SIMPSON Homer"
    Then I should see "Mardi 6 Octobre 2020"

  Scenario: Fiche santé complète pour bart mais pas pour lisa
    Then I should see "Complète"
    Then I should see "Non complète"
