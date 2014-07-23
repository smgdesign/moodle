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
 * To select whether to disable advanced features and plugins or not.
 *
 * @package    core
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$disableadvanced = optional_param('disableadvanced', false, PARAM_ALPHA);

admin_externalpage_setup('disableadvanced');
$PAGE->set_pagelayout('maintenance');
$PAGE->blocks->show_only_fake_blocks();
$adminroot = admin_get_root();

// TODO There would not be a list to this page, but here
// we could check that this is the installation process.

// Process the form and redirect.
if ($disableadvanced) {
    if ($disableadvanced === 'disable') {
        confirm_sesskey();

        // Disable advanced features.
        $data = array('s__enableoutcomes' => '0', 's__usecomments' => '0', 's__usetags' => '0',
            's__enablenotes' => '0', 's__enableportfolios' => '0', 's__enablewebservices' => '0',
            's__messaginghidereadnotifications' => '0', 's__messagingallowemailoverride' => '0',
            's__enablestats' => '0', 's__enablerssfeeds' => '0', 's__enableblogs' => '0',
            's__mnet_dispatcher_mode' => 'off', 's__enablecompletion' => '0', 's__enableavailability' => '0',
            's__enableplagiarism' => '0', 's__enablebadges' => '0');
        admin_write_settings($data);

        // TODO Set as advanced all settings that can be marked as advanced.
        // TODO Decide and disable which plugins will be disabled initially.
    }

    redirect($CFG->wwwroot);
}

$options = array(
    'default' => get_string('disableadvanceddefault', 'admin'),
    'disable' => get_string('disableadvanceddisable', 'admin')
);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('disableadvanced', 'admin'));
echo $OUTPUT->box(get_string('disableadvancedinfo', 'admin'), 'alert alert-info');

echo '<form action="disableadvanced.php" method="get" />';
echo '<fieldset>';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<div class="userinput">';
echo '<div class="fitem">';
echo '<div class="fitemelement">';
echo html_writer::select($options, 'disableadvanced', 'default', null);
echo '</div></div>';
echo '</div>';

echo '<fieldset>';
echo '<input class="form-submit" type="submit" value="'.get_string('continue').'" />';
echo '</form>';
echo $OUTPUT->footer();
