Feature: M'enregistrer
  Je m'inscris

  Background:
    Given I am on "/login"
    Then I should see "Authentification"

  Scenario: Je m'enregistre
    Then I follow "Mot de passe perdu?"
    Then I should see "Rappel du mot de passe"
    And I fill in "reset_password_request_form[email]" with "jf@marche.be"
    And I press "Envoyer"
    Then I should see "Votre compte a bien été créé, consultez votre boite mail"
