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
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2014 David Monllaó <davidm@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\plugininfo;

use moodle_url, part_of_admin_tree, admin_settingpage, admin_externalpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for storage plugins.
 *
 * @package    core
 * @copyright  2014 David Monllaó <davidm@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class storage extends base {

    protected static $defaultmethod = 'filesystem';

    /**
     * Returns whether a storage plugin can be uninstalled or not.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        global $DB;

        if (in_array($this->name, array('filesystem'))) {
            return false;
        }
        return true;
    }

    /**
     * Returns the storage settings URL.
     *
     * @return moodle_url
     */
    public static function get_manage_url() {
        global $CFG;
        return new moodle_url('/' . $CFG->admin . '/settings.php', array('section' => 'managestorages'));
    }

    /**
     * Name of the settings page.
     *
     * @return string
     */
    public function get_settings_section_name() {
        return 'storage_' . $this->name . '_settings';
    }

    /**
     * Loads storage plugins settings pages into the administration tree.
     *
     * @param part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig
     * @return void
     */
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$hassiteconfig) {
            return;
        }

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            $settings = new admin_settingpage($section, $this->displayname,
                'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Returns the default storage method's plugin name.
     *
     * @return string
     */
    public static function get_default_method() {
        return self::$defaultmethod;
    }

}
