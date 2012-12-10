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
 * Generic form field
 *
 * @package    core
 * @category   behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Mink\Element\NodeElement as NodeElement;

require_once($CFG->libdir . '/behat/form_field/behat_form_field.php');

/**
 * Generic form field
 *
 * Used by form fields without specific needs iwhen settings it's values
 *
 * @package    core
 * @category   behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_default extends behat_form_field {


    /**
     * Sets the value to a field
     * @param NodeElement $fieldnode The node where the value has to be set
     * @param string $value
     */
    public function set_value(NodeElement $field, $value) {
        $field->setValue($value);
    }
}

