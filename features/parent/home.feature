Feature: Test des pages parents
  Je suis sur la page d'accueil
  Je suis sur la page contact et j'envoie le formulaire

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: Je me loggue
    Then I should see "Votre (Vos) enfant(s)"
    Then I should see "SIMPSON Lisa"
    Then I should see "SIMPSON Homer"
