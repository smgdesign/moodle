@MDL-42013
Feature: Test MDL-42013

  Scenario: SHOULD FAIL
    Given I log in as "admin"
    Then I should not see "Home"
    And I follow "Home"

  @javascript
  Scenario: SHOULD FAIL
    Given I log in as "admin"
    Then I should not see "Home"
    And I follow "Home"

  @javascript
  Scenario: Provided wrong behaviour - SHOULD FAIL NOW
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
    And the following "categories" exists:
      | name  | category | idnumber |
      | cat1  | 0        | cat1     |
      | cat2  | 0        | cat2     |
      | cat3  | 0        | cat3     |
      | cat31 | cat3     | cat31    |
      | cat32 | cat3     | cat32    |
      | cat33 | cat3     | cat33    |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course1  | c1        | cat1     |
      | Course2  | c2        | cat2     |
      | Course31 | c31       | cat31    |
      | Course32 | c32       | cat32    |
      | Course331| c331      | cat33    |
      | Course332| c332      | cat33    |
    And the following "course enrolments" exists:
      | user     | course | role    |
      | student1 | c1     | student |
      | student1 | c31    | student |
      | student1 | c331   | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | Show my course categories | 0 |
    And I log out
    And I log in as "student1"
    When I follow "My home"
    Then I should not see "cat1" in the "Navigation" "block"
    And I should not see "cat2" in the "Navigation" "block"
    And I should not see "c1" in the "Navigation" "block"
    And I should see "c31" in the "Navigation" "block"
    And I should see "c331" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should not see "c32" in the "Navigation" "block"
    And I should not see "c332" in the "Navigation" "block"

  @javascript
  Scenario: Hover a non visible text - SHOULD FAIL
    Given I am on homepage
    And I log in as "admin"
    And I should see "Permissions"

  @javascript
  Scenario: Hover a visible text - SHOULD PASS
    Given I am on homepage
    And I log in as "admin"
    And I should see "Available courses"

  @javascript
  Scenario: Hover a visible text inside another one - SHOULD PASS
    Given I am on homepage
    And I log in as "admin"
    And I should see "Acceptance test site" in the "#page-header" "css_element"

  @javascript
  Scenario: Hover a non visible text inside another one - SHOULD FAIL
    Given I am on homepage
    And I log in as "admin"
    And I should see "Permissions" in the ".block_settings" "css_element"
