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
 * CLI tool to manage behat sites.
 *
 * @package    tool_behat
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!.
}

// Basic functions.
require_once(__DIR__ . '/../../../../lib/clilib.php');
require_once(__DIR__ . '/../../../../lib/behat/lib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'    => false,
        'install' => false,
        'drop'    => false,
        'enable'  => false,
        'disable' => false,
        'siteid'  => false,
        'diag'    => false
    ),
    array(
        'h' => 'help'
    )
);

if ($options['install'] or $options['drop']) {
    define('CACHE_DISABLE_ALL', true);
}

// Checking util.php CLI script usage.
$help = "
Internal script, use admin/tool/behat/util.php instead to keep test site sync.

Options:
--install  Installs the test environment for acceptance tests
--drop     Drops the database tables and the dataroot contents
--enable   Enables test environment and updates tests list
--disable  Disables test environment
--siteid   The test site id
--diag     Get behat test environment status code

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util_single_instance.php --install --site=1
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

if (!isset($options['siteid']) || !is_numeric($options['siteid'])) {
    echo $help;
    exit(0);
}

// Checking $CFG->behat_* vars and values.
define('BEHAT_UTIL', true);
define('CLI_SCRIPT', true);
define('ABORT_AFTER_CONFIG', true);
define('NO_OUTPUT_BUFFERING', true);

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Getting $CFG data.
require_once(__DIR__ . '/../../../../config.php');

// Ensure the main $CFG->behat_* data is ok.
behat_check_cfg();

// Override with site specifics from now.
$CFG->behat_dataroot = behat_get_site_dataroot($options['siteid']);
$CFG->behat_prefix = behat_get_site_prefix($options['siteid']);

behat_create_dataroot();

// Overrides vars with behat-test ones.
$vars = array('wwwroot', 'prefix', 'dataroot');
foreach ($vars as $var) {
    $CFG->{$var} = $CFG->{'behat_' . $var};
}

$CFG->noemailever = true;
$CFG->passwordsaltmain = 'moodle';

$CFG->themerev = 1;
$CFG->jsrev = 1;

// Unset cache and temp directories to reset them again with the new $CFG->dataroot.
unset($CFG->cachedir);
unset($CFG->tempdir);

// Continues setup.
define('ABORT_AFTER_CONFIG_CANCEL', true);
require("$CFG->dirroot/lib/setup.php");

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/pluginlib.php');
require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/testing/classes/test_lock.php');

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// Behat utilities.
require_once($CFG->libdir . '/behat/classes/util.php');
require_once($CFG->libdir . '/behat/classes/behat_command.php');

// Run command (only one per time).
if ($options['install']) {
    behat_util::install_site();
    mtrace("Acceptance test site {$options['siteid']} installed");
} else if ($options['drop']) {
    // Ensure no tests are running.
    test_lock::acquire('behat');
    behat_util::drop_site();
    mtrace("Acceptance tests site {$options['siteid']} dropped");
} else if ($options['enable']) {
    behat_util::start_test_mode();
    $runtestscommand = behat_command::get_behat_command() . ' --config '
        . $CFG->behat_dataroot . DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR . 'behat.yml';
    mtrace("Acceptance tests environment for {$options['siteid']} site enabled");
} else if ($options['disable']) {
    behat_util::stop_test_mode();
    mtrace("Acceptance tests environment for {$options['siteid']} disabled");
} else if ($options['diag']) {
    $code = behat_util::get_behat_status();
    exit($code);
} else {
    echo $help;
}

exit(0);
