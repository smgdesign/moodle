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
 * Theme-related steps.
 *
 * @package   core
 * @category  test
 * @copyright 2014 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Moodle\BehatExtension\Exception\SkippedException as SkippedException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Step definitions that applies to Moodle themes.
 *
 * @package   core
 * @category  test
 * @copyright 2014 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme extends behat_base {

    /**
     * Checks that the theme in use is based on the theme specified by it's name. Skips the scenario if it is not.
     *
     * @Given /^a theme based in "(?P<basethemename_string>(?:[^"]|\\")*)" is selected$/
     *
     * @throws SkippedException
     * @throws ExpectationException
     * @param string $basethemename
     * @return void
     */
    public function a_theme_based_in_is_selected($basethemename) {
        global $CFG;

        $parenttheme = strtolower($basethemename);

        if (!file_exists(__DIR__ . '/../../../theme/' . $parenttheme)) {
            $msg = 'The base theme "' . $basethemename. '" you specified does not exist,' .
                ' use the theme identifier name (for example base or bootstrapbase)';
            throw new ExpectationException($msg, $this->getSession());
        }

        if (!$this->is_parent($parenttheme, $CFG->theme)) {
            throw new SkippedException();
        }
    }

    /**
     * Checks if the specified theme is one of the child's parents or grandpas.
     *
     * @param string $parent
     * @param string $child
     * @return bool
     */
    protected function is_parent($parent, $child) {

        $childconfig = theme_config::load($child);

        if (empty($childconfig->parents)) {
            return false;
        }

        foreach ($childconfig->parents as $childparent) {

            if ($childparent == $parent) {
                return true;
            }

            // Look for the parents of the parent.
            if ($this->is_parent($parent, $childparent)) {
                return true;
            }
        }

        return false;
    }
}
