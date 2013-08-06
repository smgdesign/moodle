@MDL-40566
Feature: Test MDL-40566
  In order to confirm that MDL-40566 patch works as expected
  As a proud moodle tester
  I need to run and pass the test

  Background:
    Given I log in as "admin"
    When I click on "This month" "link" in the "region-post" "region"
    Then I should see "Mon" in the "Tue" "table_row"
    And I should see "Hide global events" in the "region-post" "region"
    And I click on "Admin User" "link" in the "page-footer" "region"
    And I click on "Home" "link" in the "region-pre" "region"
    And I click on "Admin User" "link" in the "page-header" "region"
    And I click on "Edit profile" "link" in the "region-pre" "region"

  @javascript
  Scenario: JS enabled
    Then I click on "Unmask" "checkbox" in the "General" "fieldset"
    And I click on "Update profile" "button" in the "region-main" "region"
    And I click on "Home" "link" in the "page-header" "region"
    And I click on "Backup" "link" in the "block_settings" "block"
    And I click on "Backup" "link" in the "Administration" "block"
    And I click on "Cancel" "button" in the "region-main" "region"
    And I click on "Cancel" "button" in the "Cancel backup" "dialogue"

  Scenario: JS disabled
