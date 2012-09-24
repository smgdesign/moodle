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
 * Output rendering for JSUnit
 *
 * @package    tool_jsunit
 * @copyright  2012 David Monllaó <david.monllao@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Rendering class for JS Unit
 */
class tool_jsunit_renderer extends plugin_renderer_base {

    /**
     * Outputs the page heading and the JS required to output the results
     *
     * From $jsunit it only gets the tests to be executed
     *
     * @param   jsunit $jsunit
     * @return  string
     */
    public function render_tool_jsunit(tool_jsunit $jsunit) {

        $o = $this->heading(get_string('title', 'tool_jsunit'));
        $o .= $this->box_start('generalbox', 'junit_test_results');
        $o .= $this->box_end();

        $this->page->requires->string_for_js('failed', 'tool_jsunit');
        $this->page->requires->string_for_js('ignored', 'tool_jsunit');
        $this->page->requires->string_for_js('message', 'tool_jsunit');
        $this->page->requires->string_for_js('name', 'tool_jsunit');
        $this->page->requires->string_for_js('notests', 'tool_jsunit');
        $this->page->requires->string_for_js('passed', 'tool_jsunit');
        $this->page->requires->string_for_js('result', 'tool_jsunit');
        $this->page->requires->string_for_js('total', 'tool_jsunit');
        $this->page->requires->yui_module('moodle-tool_jsunit-jsunit', 'M.tool_jsunit.init', array(array('testcases' => $jsunit->get_tests())));

        return $o;
    }
}
