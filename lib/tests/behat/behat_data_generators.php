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
 * Data generators for acceptance testing
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../behat/behat_base.php');
require_once(__DIR__ . '/../../phpunit/classes/util.php');
require_once(__DIR__ . '/../../phpunit/generatorlib.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Exception\PendingException as PendingException;

/**
 * Steps definitions only used to set up the test environment
 *
 * @package    core
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_data_generators extends behat_base {

    /**
     * Each element specifies:
     * - The data generator sufix used
     * - The required fields
     * - The mapping between other elements references and database field names
     * @var array
     */
    private static $elements = array(
        'users' => array(
            'datagenerator' => 'user',
            'required' => array('username')
        ),
        'categories' => array(
            'datagenerator' => 'category',
            'required' => array('idnumber'),
            'switchids' => array('category' => 'parent')
        ),
        'courses' => array(
            'datagenerator' => 'course',
            'required' => array('shortname'),
            'switchids' => array('category' => 'category')
        ),
        'groups' => array(
            'datagenerator' => 'group',
            'required' => array('idnumber', 'course'),
            'switchids' => array('course' => 'courseid')
        ),
        'groupings' => array(
            'datagenerator' => 'grouping',
            'required' => array('idnumber', 'course'),
            'switchids' => array('course' => 'courseid')
        ),
        'course enrolments' => array(
            'datagenerator' => 'enrolment',
            'required' => array('user', 'course', 'role'),
            'switchids' => array('user' => 'userid', 'course' => 'courseid', 'role' => 'roleid')

        ),
        'group members' => array(
            'datagenerator' => 'group_member',
            'required' => array('user', 'group'),
            'switchids' => array('user' => 'userid', 'group' => 'groupid')
        ),
        'grouping groups' => array(
            'datagenerator' => 'grouping_group',
            'required' => array('grouping', 'group'),
            'switchids' => array('grouping' => 'groupingid', 'group' => 'groupid')
        )
    );

    /**
     * Creates the specified entity. More info in http://docs.moodle.org/dev/Acceptance_testing#Fixtures
     *
     * @Given /^the following "(?P<element>[^"]*)" exists:$/
     *
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    public function the_following_exists($elementname, TableNode $data) {

        if (empty(self::$elements[$elementname])) {
            throw new PendingException($elementname . ' data generator is not implemented');
        }

        $datagenerator = phpunit_util::get_data_generator();

        $elementdatagenerator = self::$elements[$elementname]['datagenerator'];
        $requiredfields = self::$elements[$elementname]['required'];
        if (!empty(self::$elements[$elementname]['switchids'])) {
            $switchids = self::$elements[$elementname]['switchids'];
        }

        foreach ($data->getHash() as $elementdata) {

            // Check all the required fields.
            foreach ($requiredfields as $requiredfield) {
                if (!isset($elementdata[$requiredfield])) {
                    throw new Exception($elementname . ' requires the field ' . $requiredfield . ' to be specified');
                }
            }

            // Switch from human-friendly references to ids.
            if (isset($switchids)) {
                foreach ($switchids as $element => $field) {
                    $methodname = 'get_' . $element . '_id';

                    // Not all the switch fields are required, default vars will be assigned by data generators.
                    if (isset($elementdata[$element])) {
                        // Temp $id var to avoid problems when $element == $field.
                        $id = $this->{$methodname}($elementdata[$element]);
                        unset($elementdata[$element]);
                        $elementdata[$field] = $id;
                    }
                }
            }

            // Preprocess the entities that requires a special treatment.
            if (method_exists($this, 'preprocess_' . $elementdatagenerator)) {
                $elementdata = $this->{'preprocess_' . $elementdatagenerator}($elementdata);
            }

            // Creates element.
            $methodname = 'create_' . $elementdatagenerator;
            if (method_exists($datagenerator, $methodname)) {
                $datagenerator->{$methodname}($elementdata);
            } else {
                throw new PendingException($elementname . ' data generator is not implemented');
            }
        }

    }

    /**
     * If password is not set it uses the username
     * @param array $data
     * @return array
     */
    private function preprocess_user($data) {
        if (!isset($data['password'])) {
            $data['password'] = $data['username'];
        }
        return $data;
    }

    /**
     * Gets the user id from it's username
     * @param string $idnumber
     * @throws Exception
     * @return int
     */
    private function get_user_id($username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', array('username' => $username))) {
            throw new Exception('The specified user with username "' . $username . '" does not exists');
        }
        return $id;
    }

    /**
     * Gets the role id from it's shortname
     * @param string $idnumber
     * @throws Exception
     * @return int
     */
    private function get_role_id($roleshortname) {
        global $DB;

        if (!$id = $DB->get_field('role', 'id', array('shortname' => $roleshortname))) {
            throw new Exception('The specified role with shortname"' . $roleshortname . '" does not exists');
        }

        return $id;
    }

    /**
     * Gets the category id from it's idnumber
     * @param string $idnumber
     * @throws Exception
     * @return int
     */
    private function get_category_id($idnumber) {
        global $DB;

        // If no category was specified use the data generator one.
        if ($idnumber == false) {
            return null;
        }

        if (!$id = $DB->get_field('course_categories', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified category with idnumber "' . $idnumber . '" does not exists');
        }

        return $id;
    }

    /**
     * Gets the course id from it's shortname
     * @param string $shortname
     * @throws Exception
     * @return int
     */
    private function get_course_id($shortname) {
        global $DB;

        if (!$id = $DB->get_field('course', 'id', array('shortname' => $shortname))) {
            throw new Exception('The specified course with shortname"' . $shortname . '" does not exists');
        }
        return $id;
    }

    /**
     * Gets the group id from it's idnumber
     * @param string $idnumber
     * @throws Exception
     * @return int
     */
    private function get_group_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('groups', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified group with idnumber "' . $idnumber . '" does not exists');
        }
        return $id;
    }

    /**
     * Gets the grouping id from it's idnumber
     * @param string $idnumber
     * @throws Exception
     * @return int
     */
    private function get_grouping_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('groupings', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified grouping with idnumber "' . $idnumber . '" does not exists');
        }
        return $id;
    }
}
