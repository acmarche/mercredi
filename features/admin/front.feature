Feature: Front view
  Je test la home page
  Je test la vue par mois
  Je test la vue par semaine

  Scenario: Homepage
    Given I am on homepage
    Then the response status code should be 200
    Then I should see "Front du Mercredi"

  Scenario: Je me loggue
    Given I am on homepage
    Then the response status code should be 200
    Then I follow "Admin"
    Then I should see "Authentification"
    And I fill in "username" with "jf@marche.be"
    And I fill in "password" with "homer"
    And I press "Me connecter"
    Then I should see "Admin mercredi"
