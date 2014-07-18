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
 * Definition of a class stored_file.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/filestorage/file_progress.php');

/**
 * Class representing local files stored in a sha1 file pool.
 *
 * Since Moodle 2.0 file contents are stored in sha1 pool and
 * all other file information is stored in new "files" database table.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class stored_file {

    /**
     * @var int Indicates a file handle of the type returned by fopen.
     */
    const FILE_HANDLE_FOPEN = 0;

    /**
     * @var int Indicates a file handle of the type returned by gzopen.
     */
    const FILE_HANDLE_GZOPEN = 1;

    private $storedfile;

    /**
     * Constructor, this constructor should be called ONLY from the \core_storage\storage class!
     *
     * @param \core_storage\storage $fs file  storage instance
     * @param stdClass $file_record description of file
     * @param string $filedir location of file directory with sh1 named content files
     */
    public function __construct(\core_storage\storage $fs, stdClass $file_record, $filedir) {
        $this->storedfile = new \storage_filesystem\stored_file($fs, $file_record, $filedir);
        return $this;
    }

    /**
     * Forwarding all calls to \storage_filesystem\stored_file.
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments) {
        return call_user_func_array(
            array($this->storedfile, $name),
            $arguments
        );
    }

    /**
     * Forwarding all calls to \storage_filesystem\stored_file.
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public static function __callStatic($name, $arguments) {
        return forward_static_call_array(
            array('\\storage_filesystem\\stored_file', $name),
            $arguments
        );
    }
}
