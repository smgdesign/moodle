<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * General use steps definitions
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Cross component steps definitions
 *
 * Basic web application definitions from MinkExtension and
 * BehatchExtension. Definitions modified according to our needs
 * when necessary and including only the ones we need.
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_general extends behat_base {

    private $timeout = 10;

    /**
     * Opens homepage.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @Given /^I am on homepage$/
     */
    public function i_am_on_homepage() {
        $this->getSession()->visit($this->locatePath('/'));
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I press "(?P<button_string>(?:[^"]|\\")*)"$/
     */
    public function press_button($button) {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I follow "(?P<link_string>(?:[^"]|\\")*)"$/
     */
    public function click_link($link) {
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I fill in "(?P<field_string>(?:[^"]|\\")*)" with "(?P<value_string>(?:[^"]|\\")*)"$/
     */
    public function fill_field($field, $value) {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I select "(?P<option_string>(?:[^"]|\\")*)" from "(?P<select_string>(?:[^"]|\\")*)"$/
     */
    public function select_option($option, $select) {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);

        // We add the click event to deal with autosubmit drop down menus.
        $selectnode = $this->getSession()->getPage()->findField($select);
        if ($selectnode == null) {
            throw new ExpectationException('There is no "' . $select . '" select', $this->getSession());
        }
        $selectnode->selectOption($option);
        $selectnode->click();
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I check "(?P<option_string>(?:[^"]|\\")*)"$/
     */
    public function check_option($option) {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @When /^I uncheck "(?P<option_string>(?:[^"]|\\")*)"$/
     */
    public function uncheck_option($option) {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * Waits X seconds. Required after an action that requires data from an AJAX petition
     *
     * @Then /^I wait "(?P<seconds_number>\d+)" seconds$/
     * @param int $seconds
     */
    public function i_wait_seconds($seconds) {
        $this->getSession()->wait($seconds * 1000, false);
    }

    /**
     * Waits until the page is completely loaded. This step is auto-executed after every step
     *
     * @Given /^I wait until the page is ready$/
     */
    public function wait_until_the_page_is_ready() {
        $this->getSession()->wait($this->timeout, '(document.readyState === "complete")');
    }

    /**
     * Mouse over a CSS element
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @When /^I hover "(?P<element_string>(?:[^"]|\\")*)"$/
     * @param string $element
     */
    public function i_hover($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new ExpectationException('The hovered element "' . $element . '" was not found anywhere in the page', $this->getSession());
        }
        $node->mouseOver();
    }

    /**
     * Checks, that page contains specified text.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)"$/
     */
    public function assert_page_contains_text($text) {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that page doesn't contain specified text.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)"$/
     */
    public function assert_page_not_contains_text($text) {
        $this->assertSession()->pageTextNotContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that element with specified CSS contains specified text.
     *
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<element_string>(?:[^"]|\\")*)" element$/
     */
    public function assert_element_contains_text($element, $text) {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks, that element with specified CSS doesn't contain specified text.
     *
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<element_string>(?:[^"]|\\")*)" element$/
     */
    public function assert_element_not_contains_text($element, $text) {
        $this->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is checked.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should be checked$/
     */
    public function assert_checkbox_checked($checkbox) {
        $this->assertSession()->checkboxChecked($checkbox);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should not be checked$/
     */
    public function assert_checkbox_not_checked($checkbox) {
        $this->assertSession()->checkboxNotChecked($checkbox);
    }

    /**
     * Checks, that element with given CSS is disabled
     *
     * @see Sanpi/Behatch/Context/BrowserContext
     * @Then /^the element "(?P<element_string>(?:[^"]|\\")*)" should be disabled$/
     * @param string $element
     */
    public function the_element_should_be_disabled($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new ExpectationException('There is no "' . $element . '" element', $this->getSession());
        }

        if (!$node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not disabled', $this->getSession());
        }
    }

    /**
     * Checks, that element with given CSS is enabled
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the element "(?P<element_string>(?:[^"]|\\")*)" should be enabled$/
     * @param string $element
     */
    public function the_element_should_be_enabled($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new ExpectationException('There is no "' . $element . '" element', $this->getSession());
        }

        if ($node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not enabled', $this->getSession());
        }
    }

    /**
     * Checks, that given select box contains the specified option
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $optionstext = $this->getSession()->getPage()->findField($select)->getText();

        if (!$this->assertContains($option, $optionstext)) {
            throw new ExpectationException('The ' . $select . ' select box does not contain the ' . $option . ' option', $this->getSession());
        }
    }

    /**
     * Checks, that given select box does not contain the specified option
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should not contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_not_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $optionstext = $this->getSession()->getPage()->findField($select)->getText();

        if ($this->assertContains($option, $optionstext)) {
            throw new ExpectationException('The ' . $select . ' select box contains the ' . $option . ' option', $this->getSession());
        }
    }

}
