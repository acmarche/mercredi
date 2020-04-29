Feature: Front view
  Je test la home page
  Je test la vue par mois
  Je test la vue par semaine

  Scenario: Homepage
    Given I am on homepage
    Then the response status code should be 200
    Then I should see "Aujourd'hui"
    And I should see "Mercredi"

  Scenario: Vue par mois
    Given I am on homepage
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"

  Scenario: Vue par semaine
    Given I am on homepage
    When I follow this week
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"

  Scenario: Vue par jour
    Given I am on homepage
    Then I follow this day
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"
