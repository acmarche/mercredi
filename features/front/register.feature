Feature: Test des pages front
  Je m'inscris


  Background:
    Given I am on "/"
    Then I should see "Bienvenue"

  Scenario:
    Then I follow "vous pouvez en créer un"
    Then I should see "Plus de contacts"
    And I fill in "registration_form[nom]" with "Burn"
    And I fill in "registration_form[prenom]" with "Charles"
    And I fill in "registration_form[email]" with "burn@hotmail.com"
    And I fill in "registration_form[plainPassword]" with "Montgomery"
    And I press "M'enregistrer"
    Then I should see "Le message a bien été envoyé."

