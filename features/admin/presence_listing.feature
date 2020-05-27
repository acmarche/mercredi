Feature: Gestion des présences
  Rechercher par mois
  Rechercher par mois mauvaise date
  Rechercher par listing

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/presence/by/month"
    Then I should see "Liste des présences par mois"

  Scenario: Rechercher par mois
    Then I fill in "search_presence_by_month[mois]" with "09/2020"
    And I press "Rechercher"
    Then I should see "PERET Merlin"
    Then I should see "FERNANDEL Yves"
    Then I should see "SIMPSON Lisa"
    Then I should see "02/09/2020"
    Then I should see "03/05/2016"
    And I follow "Par défaut"

  #  Then the "content-length" response header exists
  #  Then the "content-type" response header is "xls"


  Scenario: Rechercher par mois mauvaise date
    Then I fill in "search_presence_by_month[mois]" with "09 2020"
    And I press "Rechercher"
    Then I should see "Mauvais format de date"

  Scenario: Rechercher présences
    Given I am on "/admin/presence"
    Then I should see "Liste des présences"
    Then I select "16-09-2020" from "search_presence[jour]"
    Then I select "Aye" from "search_presence[jour]"
    Then I fill in "search_presence_by_month[mois]" with "09/2020"
    And I press "Rechercher"
    Then I should see "PERET Merlin"
    Then I should see "FERNANDEL Yves"
    Then I should see "SIMPSON Lisa"
    Then I should see "16 septembre 2020"
