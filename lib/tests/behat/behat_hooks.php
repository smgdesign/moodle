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
 * Behat hooks steps definitions
 *
 * @package    core_navigation
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../behat/behat_base.php');
require_once(__DIR__ . '/../../phpunit/classes/util.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Behat\Event\ScenarioEvent as ScenarioEvent;
use Behat\Behat\Event\StepEvent as StepEvent;

/**
 * Hooks to the behat process
 *
 * Behat accepts hooks after and before each
 * suite, feature, scenario and step
 *
 * They can not call other steps as part of their process
 * like regular steps definitions does
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_hooks extends behat_base {

    private $timeout = 10;

    /**
     * Resets the database
     *
     * @BeforeScenario
     */
    public function before_scenario($event) {
        global $DB;

        $this->use_moodle_codebase();
        phpunit_util::reset_database();
        phpunit_util::reset_dataroot();

        // Assing valid data to admin user.
        $user = $DB->get_record('user', array('username' => 'admin'));
        $user->email = 'moodle@moodlemoodle.com';
        $user->firstname = 'Admin';
        $user->lastname = 'User';
        $user->city = 'Perth';
        $user->country = 'AU';
        $DB->update_record('user', $user);
    }

    /**
     * Checks that all DOM is ready
     *
     * Executed only when running against a browser
     *
     * @AfterStep @javascript
     */
    public function after_step_javascript($event) {

        if ($event->getResult() != StepEvent::PASSED ||
            !$event->hasDefinition()) {
            return;
        }

        // TODO Store and check getSession()->getCurrentUrl() to avoid executing it at every step
        $this->getSession()->wait($this->timeout, '(document.readyState === "complete")');
    }

}