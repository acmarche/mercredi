Feature: Test de la gestion des présences
  J' ajoute une présence pour un enfant qui n'a pas de fiche santé
  J' ajoute une présence pour un enfant qui a une fiche santé
  Je ne peux pas supprimer une présence passée

  Background:
    Given I am login with user "albert@marche.be" and password "homer"
    Given I am on "/parent"

  Scenario: J' ajoute une présence pour un enfant qui n'a pas de fiche santé
    When I follow "Jour d'accueil"
    Then I follow "SIMPSON Lisa"
    Then I should see "La fiche santé de votre enfant doit être complétée"

  Scenario: J' ajoute une présence pour un enfant qui a une fiche santé
    When I follow "Jour d'accueil"
    Then I follow "SIMPSON Bart"
    Then I should see "Sélectionnez des jours d'accueil"
    When I select "Mercredi 7 octobre 2020" from "presence_new_for_parent_jours"
    When I additionally select "Mercredi 16 septembre 2020" from "presence_new_for_parent_jours"
    And I press "Sauvegarder"
    Then I should see "La présence a bien été ajoutée"

 Scenario: Je ne peux pas supprimer une présence passée
    Then I follow "SIMPSON Bart"
    Then I should see "Mercredi 6 mai 2020"
    Then I follow "Mercredi 6 mai 2020"
    Then I should see "Détail de la présence de SIMPSON Bart du mercredi 6 mai 2020"
    Then I press "Supprimer la présence"
    Then I should see "Une présence passée ne peut être supprimée"
