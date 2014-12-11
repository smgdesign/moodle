<?php

// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

// THIS TEST SCRIPT IS DISABLING ALL WS REQUIRED PARAMS JUST TO CHECK THAT WE RECEIVE THE EXCEPTION.

// SETUP - NEED TO BE CHANGED FOR AN ADMIN USER WITH ACCESS TO ALL SETTINGS.
$token = 'd02b6d5360685ad81e24c0d1ebaf25b8';
$domainname = 'http://localhost/master';

$deprecatedservices = array(
    'moodle_course_create_courses',
    'moodle_course_get_courses',
    'moodle_enrol_get_enrolled_users',
    'moodle_enrol_get_users_courses',
    'moodle_enrol_manual_enrol_users',
    'moodle_file_get_files',
    'moodle_file_upload',
    'moodle_group_add_groupmembers',
    'moodle_group_create_groups',
    'moodle_group_delete_groupmembers',
    'moodle_group_delete_groups',
    'moodle_group_get_course_groups',
    'moodle_group_get_groupmembers',
    'moodle_group_get_groups',
    'moodle_message_send_instantmessages',
    'moodle_notes_create_notes',
    'moodle_role_assign',
    'moodle_role_unassign',
    'moodle_user_create_users',
    'moodle_user_delete_users',
    'moodle_user_get_course_participants_by_id',
    'moodle_user_get_users_by_courseid',
    'moodle_user_get_users_by_id',
    'moodle_user_update_users',
    'core_grade_get_definitions',
    'core_user_get_users_by_id',
    'moodle_webservice_get_siteinfo'
);

// We can probably use require_once('config.php') but I prefer to separate concepts.
require_once(__DIR__ . '/curl.php');

// We call each deprecated service to ensure all of them returs a deprecated_external_function exception.
foreach ($deprecatedservices as $deprecatedservice) {

    $serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$deprecatedservice;

    $curl = new curl();
    $resp = json_decode($curl->post($serverurl . '&moodlewsrestformat=json', $params));
    if (empty($resp->exception) || $resp->exception !== 'deprecated_external_function') {
        echo "ERROR: Noooo, $deprecatedservice should be returning a deprecated step exception and it is returning the following response instead:";
        var_dump($resp);
        exit(1);
    }
}

echo "Ok, It passes 100%. Mark the issue as passed and send kudos to the assignee" . PHP_EOL;
