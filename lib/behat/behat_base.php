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
     * Runs config.php to use moodle codebase functionality
     *
     * This method must only be called by steps definitions which
     * sets up the testing environment (white box testing) never
     * by steps definitions who are supposed to test the user
     * interaction with the system
     */
    protected function use_moodle_codebase() {

        define('BEHAT_RUNNING', 1);
        define('CLI_SCRIPT', 1);

        require_once(__DIR__ . '/../../config.php');
    }

}

