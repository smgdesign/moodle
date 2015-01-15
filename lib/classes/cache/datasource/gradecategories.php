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
 * Data source for gradecategories cache definition.
 *
 * @package    core
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\cache\datasource;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/grade/grade_category.php');

/**
 * Class to get / set grade categories data into the cache.
 *
 * @package   core
 * @copyright 2015 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradecategories implements \cache_data_source {

    /**
     * @var \core\cache\datasource\gradecategories $datasource
     */
    protected static $datasource = null;

    /**
     * Returns an instance of the data source class.
     *
     * @return \core\cache\datasource\gradecategories
     */
    protected static function instance() {
        if (is_null(self::$datasource)) {
            self::$datasource = new \core\cache\datasource\gradecategories();
        }
        return self::$datasource;
    }

    /**
     * Returns an instance of the data source class that the cache can use for loading data using the other methods
     * specified by this interface.
     *
     * @param \cache_definition $definition
     * @return \core\cache\datasource\gradecategories
     */
    public static function get_instance_for_cache(\cache_definition $definition) {
        return self::instance();
    }

    /**
     * Gets the grade category.
     *
     * @param int $id
     * @return \grade_category|bool The \grade_category object or false if it can not be located.
     */
    public static function get($id) {
        return self::instance()->get_definition()->get($id);
    }

    /**
     * Deletes the definition as part of an invalidation.
     *
     * @param int $id
     * @return bool
     */
    public static function uncache($id) {
        return self::instance()->get_definition()->delete($id);
    }

    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * @param int The grade category id.
     * @return \grade_category|bool The \grade_category object or false if it can't be loaded.
     */
    public function load_for_cache($key) {
        if (!$gradecategories = $this->load_many_for_cache(array($key))) {
            return false;
        }

        return array_shift($gradecategories);
    }

    /**
     * Loads several keys for the cache.
     *
     * @param array The grade category ids
     * @return array|bool The grade categories or false if it can't be loaded.
     */
    public function load_many_for_cache(array $keys) {

        $gradecategories = array();
        foreach ($keys as $key) {
            if ($gcat = \grade_category::fetch(array('id' => $key))) {
                $gradecategories[$key] = $gcat;
            }
        }
        return $gradecategories;
    }

    /**
     * Gets the definition.
     * @return \cache
     */
    protected function get_definition() {
        return \cache::make('core', 'gradecategories');
    }
}
