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
require_once(__DIR__ . '/../../testing/classes/test_lock.php');

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
 * like regular steps definitions does.
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
     * Includes config.php to use moodle codebase, called once per suite
     *
     * @BeforeSuite
     */
    public static function before_suite($event) {
        global $CFG;

        // iTo work with behat_dataroot and behat_prefix instead of the regular environment.
        define('BEHAT_RUNNING', 1);
        define('CLI_SCRIPT', 1);

        require_once(__DIR__ . '/../../../config.php');
        require_once(__DIR__ . '/../../behat/classes/util.php');

        // Avoids vendor/bin/behat to be executed directly without test environment enabled
        // to prevent undesired db & dataroot modifications, this is also checked
        // before each scenario (accidental user deletes) in the BeforeScenario hook

        if (!behat_util::is_test_mode_enabled()) {
            throw new Exception('Behat only can run if test mode is enabled. More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests');
        }

        if (!behat_util::is_server_running()) {
            throw new Exception($CFG->behat_wwwroot . ' is not available, ensure you started your PHP built-in server. More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests');
        }

        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');
    }

    /**
     * Resets the test environment
     *
     * @BeforeScenario
     */
    public function before_scenario($event) {
        global $DB, $SESSION, $CFG;

        // As many checks as we can.
        if (!defined('BEHAT_RUNNING') ||
               php_sapi_name() != 'cli' ||
               !behat_util::is_test_mode_enabled() ||
               !behat_util::is_test_site() ||
               !isset($CFG->originaldataroot))  {
           throw new Exception('Behat only can modify the test database and the test dataroot');
           exit(1);
        }

        behat_util::reset_database();
        behat_util::reset_dataroot();

        // Assing valid data to admin user (some generator-related code needs a valid user).
        $user = $DB->get_record('user', array('username' => 'admin'));
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

        // Just trying if server responds.
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
