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
 * Generic moodleforms field
 *
 * @package    core
 * @category   behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Mink\Session as Session,
    Behat\Mink\Element\NodeElement as NodeElement;

/**
 * Representation of a moodle field
 *
 * Basically an interface with Mink session
 *
 * @package    core
 * @category   behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_field {

    protected $session;
    protected $fieldnode;

    /**
     * Reference to Mink session to traverse/modify the page DOM
     *
     * @param Session $session
     * @param NodeElement $fieldnode The field DOM node
     * @return void
     */
    public function __construct(Session $session, NodeElement $fieldnode) {
        $this->session = $session;
        $this->field = $fieldnode;
    }

    /**
     * Sets the value to a field
     * @param string $value
     */
    public function set_value($value) {
        $this->field->setValue($value);
    }

    /**
     * Returns the current value of the select element
     *
     * @return string
     */
    public function get_value() {
        return $this->field->getValue();
    }

}
