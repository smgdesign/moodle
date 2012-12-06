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
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_general extends behat_base {

    private $timeout = 10;

    /**
     * Waits X seconds. Required after an action that requires data from an AJAX petition
     *
     * @Then /^I wait (?P<seconds>\d+) second(|s)$/
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
     * @When /^(?:|I )hover "(?P<element>[^"]*)"$/
     * @param string $element
     */
    public function i_hover($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new ExpectationException('The hovered element "' . $element . '" was not found anywhere in the page');
        }
        $node->mouseOver();
    }

    /**
     * Checks, that element with given CSS is disabled
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the element "(?P<element>[^"]*)" should be disabled$/
     * @param string $element
     */
    public function the_element_should_be_disabled($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new ExpectationException('There is no "' . $element . '" element');
        }

        if (!$node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not disabled');
        }
    }

    /**
     * Checks, that element with given CSS is enabled
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the element "(?P<element>[^"]*)" should be enabled$/
     * @param string $element
     */
    public function the_element_should_be_enabled($element) {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new ExpectationException('There is no "' . $element . '" element');
        }

        if ($node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not enabled');
        }
    }

    /**
     * Checks, that given select box contains the specified option
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the "(?P<select>[^"]*)" select box should contain "(?P<option>[^"]*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $optionstext = $this->getSession()->getPage()->findField($select)->getText();

        if (!$this->assertContains($option, $optionstext)) {
            throw new ExpectationException('The ' . $select . ' select box does not contain the ' . $option . ' option');
        }
    }

    /**
     * Checks, that given select box does not contain the specified option
     *
     * @see Sanpi/Behatch/Context/BrowserContext.php
     * @Then /^the "(?P<select>[^"]*)" select box should not contain "(?P<option>[^"]*)"$/
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_not_contain($select, $option) {

        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $optionstext = $this->getSession()->getPage()->findField($select)->getText();

        if ($this->assertContains($option, $optionstext)) {
            throw new ExpectationException('The ' . $select . ' select box contains the ' . $option . ' option');
        }
    }

    /**
     * Simple preg_match
     *
     * @param mixed $expected What to find
     * @param mixed $in Where to find it
     * @return boolean
     */
    protected function assertContains($expected, $in) {

        $regex   = '/'.preg_quote($expected, '/').'/ui';

        if (!preg_match($regex, $in)) {
            return false;
        }

        return true;
    }

}
