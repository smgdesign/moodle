@tool @tool_behat @MDL-42324
Feature: Set up contextual data for tests
  In order to write tests quickly
  As a developer
  I need to fill the database with fixtures

  Scenario Outline: Add modules
    Given the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "activities" exists:
      | activity | name | intro | course | idnumber |
      | <activitytype> | Test activity | Description | C1 | <idnumber> |
    When I log in as "admin"
    And I follow "Course 1"
    Then I should see "Test activity"

  Examples:
    | activitytype | idnumber |
    | assign | assign1 |
    | chat | chat1 |
    | choice | choice1 |
    | data | data1 |
    | lti | lti1 |
    | forum | forum1 |
    | glossary | glossary1 |
    | lesson | lesson1 |
    | quiz | quiz1 |
    | scorm | scorm1 |
    | survey | survey1 |
    | wiki | wiki1 |
    | workshop | workshop1 |
    | book | book1 |
    | file | file1 |
    | folder | folder1 |
