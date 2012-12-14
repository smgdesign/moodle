@core_behat
Feature: Solve most common selenium issues
  In order to write tests effectively
  As a moodle developers
  I don't want to deal with Selenium issues

  @javascript @wip
  Scenario:
    Given the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "File" to section "2"
    And I fill the moodle form with:
      | Name | Test file |
      | Description | Test file description |
    And I create "New folder" folder in "Select files" filepicker
    And I wait "5" seconds
