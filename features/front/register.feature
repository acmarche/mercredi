Feature: M'enregistrer
  Je m'inscris

  Background:
    Given I am on "/"
    Then I should see "Bienvenue"

  Scenario: Je m'enregistre
    Then I follow "M'enregistrer"
    And I fill in "registration_form[nom]" with "Burn"
    And I fill in "registration_form[prenom]" with "Charles"
    And I fill in "registration_form[email]" with "burn@hotmail.com"
    And I fill in "registration_form[telephone]" with "0476 22 55 88"
    And I fill in "registration_form[plainPassword]" with "Montgomery"
    And I check "Conditions d'utilisation"
    And I press "M'enregistrer"
    Then print last response
    Then I should see "Votre compte a bien été créé, consultez votre boite mail"
