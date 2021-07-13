Feature: Front view
  Je meloggue

  Scenario: Je me loggue
    Given I am on homepage
    Then the response status code should be 200
    Then I follow "Me connecter"
    Then I should see "Authentifiez-vous"
    And I fill in "username" with "jf@marche.be"
    And I fill in "password" with "homer"
    And I press "Me connecter"
    Then I should see "Ecrivez le nom de l'enfant pour un accès rapide"
