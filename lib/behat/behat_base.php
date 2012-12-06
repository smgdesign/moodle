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
 * Base class of all steps definitions
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Steps definitions base class
 *
 * To extend by the steps definitions of the different Moodle components
 *
 * It does not contain steps definitions, they will all be contained in
 * tests/behat folders depending on the target component to test
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_base extends Behat\MinkExtension\Context\RawMinkContext {

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * \\ is the chars combination to add when you
     * want to escape the " character
     *
     * @param string $argument
     * @see Behat\MinkExtension\Context\MinkContext
     * @return string
     */
    protected function fixStepArgument($argument) {
        return str_replace('\\"', '"', $argument);
    }
}

