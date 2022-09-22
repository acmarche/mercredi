Feature: M'enregistrer
  Je m'inscris

  Background:
    Given I am on "/register"
    Then I should see "M'enregistrer en tant que parent"

  Scenario: Je m'enregistre
    And I fill in "registration_form[nom]" with "Burn"
    And I fill in "registration_form[prenom]" with "Charles"
    And I fill in "registration_form[email]" with "burn@hotmail.com"
    And I fill in "registration_form[telephone]" with "0476 22 55 88"
    And I fill in "registration_form[plainPassword]" with "Montgomery"
    And I check "J'accepte les conditions d'utilisation"
    And I press "M'enregistrer"
    And the response status code should be 200
    #Then print last response
    Then I should see "Votre compte a bien été créé, consultez votre boite mail"
