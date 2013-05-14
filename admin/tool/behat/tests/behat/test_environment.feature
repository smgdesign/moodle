@tool @tool_behat @MDL-39528
Feature: Set up the testing environment
  In order to execute automated acceptance tests
  As a developer
  I need to use the test environment instead of the regular environment

  @javascript
  Scenario: Accessing the site
    When I am on homepage
    Then I should see "Acceptance test site"
