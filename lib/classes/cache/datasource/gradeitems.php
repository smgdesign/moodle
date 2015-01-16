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
 * Data source for gradeitems cache definition.
 *
 * @package    core
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\cache\datasource;

defined('MOODLE_INTERNAL') || die();


/**
 * Class to get / set grade items data into the cache.
 *
 * @package   core
 * @copyright 2015 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradeitems implements \cache_data_source {

    /**
     * @var \core\cache\datasource\gradeitems $datasource
     */
    protected static $datasource = null;

    /**
     * It stores the grade item id - course relation during the request.
     *
     * Indexed by grade item id.
     *
     * We only store here the course id as this is not supposed to use
     * much memory, it is a way to get grade items from it's id without
     * requiring the course id, keeping consumer components clean without
     * unnecessary course id var dependencies.
     *
     * @var array $loadeditemsids
     */
    protected static $loadeditemsids = null;

    /**
     * Returns an instance of the data source class.
     *
     * @return \core\cache\datasource\gradeitems
     */
    protected static function instance() {
        if (is_null(self::$datasource)) {
            self::$datasource = new \core\cache\datasource\gradeitems();
        }
        return self::$datasource;
    }

    /**
     * Returns an instance of the data source class that the cache can use for loading data using the other methods
     * specified by this interface.
     *
     * @param \cache_definition $definition
     * @return \core\cache\datasource\gradeitems
     */
    public static function get_instance_for_cache(\cache_definition $definition) {
        return self::instance();
    }

    /**
     * Retrieves a list of \grade_item objects from the specified course.
     *
     * @param int $courseid
     * @param array $filter Associative array to filter the course grade items.
     * @return array|bool \grade_item instances array or false if not found.
     */
    public static function get($courseid, $filter = false) {

        if (!$coursegradeitems = self::instance()->get_definition()->get($courseid)) {
            return false;
        }

        // All the course grade items.
        if ($filter === false) {
            return $coursegradeitems;
        }

        // The \grade_items are indexed by id, so just return it.
        if (!empty($filter['id']) && count($filter) === 1) {
            if (empty($coursegradeitems[$filter['id']])) {
                return false;
            }

            // The interface explicitly returns an array, if the
            // user wants a grade_item use get_item().
            return array($filter['id'] => $coursegradeitems[$filter['id']]);
        }

        // Apply filters.
        $gradeitems = array();
        foreach ($coursegradeitems as $gradeitemid => $gradeitem) {
            $filtered = false;
            foreach ($filter as $field => $value) {
                if ($gradeitem->{$field} != $value) {
                    $filtered = true;
                    break;
                }
            }
            if ($filtered === false) {
                $gradeitems[$gradeitemid] = $gradeitem;
            }
        }

        // Even though it may be empty.
        return $gradeitems;
    }

    /**
     * Retrieves a \grade_item object from it's id.
     *
     * Note that, without providing the grade item course id,
     * this function's performance depends on whether the
     * requested item was already loaded or not, so if it is
     * not we need to query the database to find out the course id
     * and later fill the course cache, so if you just need to get
     * 1 item once, probably will be better to use \grade_item::fetch().
     *
     * @param int $id Grade item id
     * @param int $courseid Course id
     * @return \grade_item|bool A \grade_item instance or false if it was not found.
     */
    public static function get_item($id, $courseid = false) {
        global $DB;

        // Getting the course id from the previously loaded items.
        if (!$courseid && !empty(self::$loadeditemsids[$id])) {
            $courseid = self::$loadeditemsids[$id];
        }

        // Query the database.
        if (!$courseid) {
            $courseid = $DB->get_field('grade_items', 'courseid', array('id' => $id));
        }

        // This item does not exist even in the database.
        if (!$courseid) {
            return false;
        }

        // Get the item from the cache.
        if (!$items = self::get($courseid, array('id' => $id))) {
            return false;
        }

        return reset($items);
    }

    /**
     * Deletes the definition as part of an invalidation.
     *
     * @param int $courseid
     * @return bool
     */
    public static function uncache($courseid = false) {

        // Unset them all, it is per request and in most of the cases they will belong to the same course.
        self::$loadeditemsids = array();

        if ($courseid) {
            return self::instance()->get_definition()->delete($courseid);
        } else {
            return self::instance()->get_definition()->purge();
        }
    }

    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * @param int $courseid The grade item course id.
     * @return array|bool The array of the course \grade_items or false if not found.
     */
    public function load_for_cache($courseid) {
        if (!$coursesgradeitems = $this->load_many_for_cache(array($courseid))) {
            return false;
        }

        return array_shift($coursesgradeitems);
    }

    /**
     * Loads several keys for the cache.
     *
     * @param array $courseids grade item course ids.
     * @return array|bool The grade items or false if it can't be loaded.
     */
    public function load_many_for_cache(array $courseids) {

        $coursesgradeitems = array();
        foreach ($courseids as $courseid) {
            if ($gradeitems = \grade_item::fetch_all(array('courseid' => $courseid))) {
                $coursesgradeitems[$courseid] = $gradeitems;

                // Populate self::$loadeditemsids to allow grade items
                // to be retrieved just by their id (without course id).
                foreach ($gradeitems as $gradeitemid => $gradeitemdata) {
                    self::$loadeditemsids[$gradeitemid] = $gradeitemdata->courseid;
                }
            }
        }

        return $coursesgradeitems;
    }

    /**
     * Gets the definition.
     * @return \cache
     */
    protected function get_definition() {
        return \cache::make('core', 'gradeitems');
    }
}
