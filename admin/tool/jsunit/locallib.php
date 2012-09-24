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
 * JSUnit lib
 *
 * @package    tool_jsunit
 * @copyright  2012 David Monllaó <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/pluginlib.php');

/**
 * JSUnit class
 *
 * @package    tool_junit
 * @copyright  2012 David Monllaó <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_jsunit implements renderable {

    /**
     * Dir where plugins and components stores javascript tests
     * @static
     * @var string
     */
    public static $testsdir = 'yui';

    private $tests = array();

    /**
     * Main method to process the unit tests
     */
    public function execute() {
        $this->get_plugins_with_tests();
    }

    /**
     * Returns the list of test to be executed
     * @return array Tests paths
     */
    public function get_tests() {
        return $this->tests;
    }

    /**
     * Gets all the plugins with JS tests
     *
     * Support for all the moodle plugins
     */
    private function get_plugins_with_tests() {
        global $PAGE;

        $tests = array();

        // All the moodle plugin types
        $plugintypes = get_plugin_types();
        ksort($plugintypes);

        foreach ($plugintypes as $type => $unused) {
            $plugins = get_plugin_list_with_file($type, tool_jsunit::$testsdir);
            ksort($plugins);
            if ($plugins) {
                foreach ($plugins as $plugin => $fullpath) {
                    $this->load_component_tests($fullpath, $type, $plugin);
                }
            }
        }
    }

    /**
     * Gets the test files of a directory
     * @param string $dir
     */
    public function load_component_tests($dir, $type, $plugin) {
        global $PAGE;

        $tests = array();

        $iterator = new DirectoryIterator($dir);
        foreach ($iterator as $file) {

            // The module name is the dir file name
            $yuimodule = $file->getFilename();

            // All the tests ends with *test
            if (substr($yuimodule, strlen($yuimodule) - 4) == 'test') {

                $module = 'moodle-' . $type . '_' . $plugin . '-' . $yuimodule;
                $function = 'M.' . $type . '_' . $plugin . '.init_' . $yuimodule;

                // Loading the module (is the test module responsability to load it's dependencies)
                $PAGE->requires->yui_module($module, $function);
                $this->tests[] = $function;

            }
        }
    }
}

