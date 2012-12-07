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
     * Gives access to moodle codebase
     *
     * Runs config.php to use moodle codebase, called once per suite
     *
     * @BeforeSuite
     */
    public static function before_suite($event) {
        global $CFG;

        // Used to work with phpunit_dataroot and phpunit_prefix instead of the regular environment.
        define('BEHAT_RUNNING', 1);
        define('CLI_SCRIPT', 1);

        require_once(__DIR__ . '/../../../config.php');

        // Avoids bin/behat to be executed directly without test environment enabled
        // to prevent undesired db & dataroot modifications, this is also checked in
        // the BeforeScenario hook
        require_once(__DIR__ . '/../../../admin/tool/behat/locallib.php');

        if (!tool_behat::is_test_mode_enabled()) {
            throw new Exception('Behat only can run is test mode is enabled');
        }

    }

    /**
     * Resets the test environment
     *
     * @BeforeScenario
     */
    public function before_scenario($event) {
        global $DB, $SESSION, $CFG;

        // Needs $CFG->admin and $CFG is set in before_suite().
        require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/behat/locallib.php');

        // As many checks as we can.
        if (!defined('BEHAT_RUNNING') ||
               php_sapi_name() != 'cli' ||
               !tool_behat::is_test_mode_enabled() ||
               !isset($CFG->originaldataroot))  {
           throw new Exception('Behat only can modify the test database and the test dataroot');
           exit(1);
        }

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

        // Sets maximum debug level.
        set_config('debug', DEBUG_DEVELOPER);

        session_set_user($user);

        // Avoid some notices / warnings.
        $SESSION = new stdClass();
    }

    /**
     * Ensures selenium is running
     *
     * Is only executed in scenarios which requires selenium to run,
     * it returns a direct error message about what's going on
     *
     * @BeforeScenario @javascript
     */
    public function before_scenario_javascript($event) {

        // Just trying if server responds
        try {
            $this->getSession()->executeScript('// empty comment');
        } catch (Exception $e) {
            $msg = 'Selenium server is not running, you need to start it to run tests that involves Javascript. More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests';
            throw new Exception($msg);
            exit(1);
        }
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

        // Hooks doesn't allows other steps calls.
        // TODO Store and check a static getSession()->getCurrentUrl() to avoid executing it at every step
        $this->getSession()->wait($this->timeout, '(document.readyState === "complete")');
    }

}
