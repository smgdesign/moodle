@MDL-38863
Feature: Role assign data generator
  In order to test MDL-38863
  As a cool moodle developer/integrator
  I need to check that the different contexts are properly referenced

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | user1 | User | 1 | user1@asd.com |
    And the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |

  Scenario: Referencing a course
    Given the following "role assigns" exists:
      | user | role | contextlevel | reference |
      | user1 | student | Course | C1 |
    And I log in as "admin"
    When I follow "Course 1"
    And I follow "Other users"
    Then I should see "User 1"
    And I should see "user1@asd.com"

  Scenario: Referencing a course category
    Given the following "categories" exists:
      | name | parent | idnumber |
      | Category name 1 | 0 | CC1 |
    Given the following "role assigns" exists:
      | user | role | contextlevel | reference |
      | user1 | manager | Category | CC1 |
    And I log in as "admin"
    When I follow "Add/edit courses"
    And I follow "Category name 1"
    And I follow "Assign roles"
    And I follow "Manager"
    Then the "removeselect" select box should contain "User 1 (user1@asd.com)"

  Scenario: Referencing the system
    Given the following "role assigns" exists:
      | user | role | contextlevel | reference |
      | user1 | manager | System | |
    And I log in as "admin"
    When I follow "Assign system roles"
    And I follow "Manager"
    Then the "removeselect" select box should contain "User 1 (user1@asd.com)"

  Scenario: No references, IS NORMAL THAT IT FAILS
    Given the following "role assigns" exists:
      | user | role | contextlevel | reference |
      | user1 | student | IDONTEXIST | |
