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
 * Steps definitions related with forms.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\When as When,
    Behat\Behat\Context\Step\Then as Then,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Element\NodeElement as NodeElement,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Forms-related steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_forms extends behat_base {

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
     * Fills a moodle form with field/value data.
     *
     * @Given /^I fill the moodle form with:$/
     * @param TableNode $data
     */
    public function i_fill_the_moodle_form_with(TableNode $data) {

        $datahash = $data->getRowsHash();

        // The action depends on the field type.
        foreach ($datahash as $locator => $value) {

            // Finds the element in the page.
            unset($fieldnode);
            $locator = $this->fixStepArgument($locator);
            $fieldnode = $this->getSession()->getPage()->findField($locator);
            if (null === $fieldnode) {
                throw new ElementNotFoundException(
                    $this->getSession(), 'form field', 'id|name|label|value', $locator
                );
            }

            // Gets the field type from a parent node.
            $field = $this->get_field($fieldnode, $locator);

            // Delegates to the field class.
            $field->set_value($value);
        }
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
     * Checks that the form element field have the specified value.
     *
     * @Then /^the "(?P<field_string>(?:[^"]|\\")*)" field should match "(?P<value_string>(?:[^"]|\\")*)" value$/
     * @param mixed $locator
     * @param mixed $value
     */
    public function the_field_should_match_value($locator, $value) {

        $fieldnode = $this->getSession()->getPage()->findField($locator);
        if (null === $fieldnode) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $locator
            );
        }

        // Gets the field instance.
        $field = $this->get_field($fieldnode, $locator);

        // Checks if the provided value matches the current field value.
        if ($value != $field->get_value()) {
            throw new ExpectationException(
                'The \'' . $locator . '\' value is \'' . $field->get_value() . '\'' ,
                $this->getSession()
            );
        }
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
     * Checks, that given select box contains the specified option.
     *
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);

        $selectnode = $this->getSession()->getPage()->findField($select);
        if ($selectnode == null) {
            throw new ExpectationException('There is no "' . $select . '" select', $this->getSession());
        }

        $regex = '/' . preg_quote($option, '/') . '/ui';
        if (!preg_match($regex, $selectnode->getText())) {
            throw new ExpectationException(
                'The select box "' . $select . '" does not contains the option "' . $option . '"',
                $this->getSession()
            );
        }

    }

    /**
     * Checks, that given select box does not contain the specified option.
     *
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should not contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_not_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);

        $selectnode = $this->getSession()->getPage()->findField($select);
        if ($selectnode == null) {
            throw new ExpectationException('There is no "' . $select . '" select', $this->getSession());
        }

        $regex = '/' . preg_quote($option, '/') . '/ui';
        if (preg_match($regex, $selectnode->getText())) {
            throw new ExpectationException(
                'The select box "' . $select . '" contains the option "' . $option . '"',
                $this->getSession()
            );
        }
    }

    /**
     * Gets an instance of the form element field.
     *
     * @param NodeElement $fieldnode The current node
     * @param string $locator Just to send an exception that makes sense for the user
     * @return behat_form_field
     */
    private function get_field(NodeElement $fieldnode, $locator) {
        global $CFG;

        // Get the field type.
        $type = $this->get_node_type($fieldnode, $locator);
        $classname = 'behat_form_' . $type;

        // Fallsback on the default form field if nothing specific exists.
        $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        if (!file_exists($classpath)) {
            $classname = 'behat_form_field';
            $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        }

        // Returns the instance.
        require_once($classpath);
        return new $classname($this->getSession(), $fieldnode);
    }

    /**
     * Recursive method to find the field type.
     *
     * We look for a parent node with a 'felement'
     * class to find the field type.
     *
     * @throws Exception
     * @param NodeElement $fieldnode The current node
     * @param string $locator Just to send an exception that makes sense for the user
     * @return mixed String or NodeElement depending if we have reached the felement node
     */
    private function get_node_type(NodeElement $fieldnode, $locator) {

        // We look for a parent node with 'felement' class.
        if ($class = $fieldnode->getParent()->getAttribute('class')) {

            if (strstr($class, 'felement') != false) {
                // Remove 'felement f' from class value.
                return substr($class, 10);
            }

            // Stop propagation through the DOM, something went wrong!.
            if (strstr($class, 'fcontainer') != false) {
                throw new Exception('No element type for ' . $locator . ' found');
            }
        }

        return $this->get_node_type($fieldnode->getParent(), $locator);
    }

}
