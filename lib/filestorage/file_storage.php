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
 * Core file storage class definition.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/stored_file.php");

/**
 * File storage class used for low level access to stored files.
 *
 * Only owner of file area may use this class to access own files,
 * for example only code in mod/assignment/* may access assignment
 * attachments. When some other part of moodle needs to access
 * files of modules it has to use file_browser class instead or there
 * has to be some callback API.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class file_storage {

    private $storagefilesystem;

    /**
     * Instantiates \storage_filesystem\storage delegating evenything there.
     *
     * @param string $filedir full path to pool directory
     * @param string $trashdir temporary storage of deleted area
     * @param string $tempdir temporary storage of various files
     * @param int $dirpermissions new directory permissions
     * @param int $filepermissions new file permissions
     */
    public function __construct($filedir, $trashdir, $tempdir, $dirpermissions, $filepermissions) {
        $this->storagefilesystem = new \storage_filesystem\storage($filedir, $trashdir, $tempdir, $dirpermissions, $filepermissions);

        return $this;
    }

    /**
     * Forwarding all calls to \storage_filesystem\storage.
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments) {
        return call_user_func_array(
            array($this->storagefilesystem, $name),
            $arguments
        );
    }

    /**
     * Forwarding all calls to \storage_filesystem\storage.
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public static function __callStatic($name, $arguments) {
        return forward_static_call_array(
            array('\\storage_filesystem\\storage', $name),
            $arguments
        );
    }
}
