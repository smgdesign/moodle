@MDL-43584
Feature: Moodle rules
  In order to make moodle rule over the world
  As me
  I need to make this work

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "groups" exists:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "cohorts" exists:
      | name | idnumber |
      | Cohort 1 | CH1 |
    And the following "course enrolments" exists:
      | user | course | role |
      | student1 | C1 | student |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exists:
      | activity   | name                   | intro                         | course | idnumber    |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     |
    And I log in as "admin"

  @javascript
  Scenario: ENROL COHORT SHOULD FAIL WHEN USE_DEPRECATED IS DISABLED
    When I add "student1" user to "CH1" cohort
    And I click on "Assign" "link" in the "Cohort 1" "table_row"
    Then the "Current users" select box should contain "Student 1"

  @javascript
  Scenario: ENROL GROUP SHOULD FAIL WHEN USE_DEPRECATED IS DISABLED
    When I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "student1" user to "Group 1" group
    And I select "Group 1 (1)" from "groups"
    Then the "members" select box should contain "Student 1"

  @javascript
  Scenario: TABLE ROW CONTINUES WORKING BUT SHOULD FAIL WHEN USE_DEPRECATED IS DISABLED
    When I follow "Course 1"
    And I follow "Grades"
    And I press "Turn editing on"
    Then I click on "Grades for Student 1" "link" in the "Student 1" table row
    And I should see "User report - Student 1"

  @javascript
  Scenario: I SEND MESSAGE TO SHOULD FAIL WHEN USE_DEPRECATED IS DISABLED
    When I send "I'm the message body" message to "student1"
    And I select "Recent conversations" from "Message navigation:"
    Then I should see "I'm the message body"
    And I should see "View: this conversation"
