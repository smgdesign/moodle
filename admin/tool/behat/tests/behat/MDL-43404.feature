@_cross_browser @MDL-43404
Feature: Remove form changes checker
  In order to test MDL-43404
  As $YOU
  I need to ensure that the form changes checker is disabled when running behat features

  Background:
    Given I log in as "admin"
    And I expand "My profile" node
    And I follow "View profile"

  @javascript
  Scenario: Form without modifications 1/2
    When I follow "Edit profile"
    # This scenario is useful to see that there is no problem here, like before the patch.
    Then I should see "Forum tracking"

  @javascript
  Scenario:
    When I follow "Edit profile"
    And I fill the moodle form with:
      | Surname | Macho man |
    # Here we would be in trouble before the patch.
    And I follow "Change password"
    Then I should see "The password must have at least"

  @javascript
  Scenario: Form without modifications 2/2
    When I follow "Edit profile"
    And I fill the moodle form with:
      | Surname | Macho man |
    # This scenario is useful to see that the suite and the following scenarios continues running as expected.
    And I press "Update profile"
