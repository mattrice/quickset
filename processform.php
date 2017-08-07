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

/*
 * Processes the changed settings
 * 
 * @package     block_quickset
 * @copyright   2012 Bob Puffer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    require_once('../../config.php');
    global $CFG, $PAGE, $DB;
    require_once($CFG->dirroot . '/lib/accesslib.php');
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/course/format/lib.php');
    $courseid = required_param('courseid', PARAM_INT);
    $returnurl = $_SERVER['HTTP_REFERER'];

    if ($data = data_submitted() and confirm_sesskey()) {
        $context = context_course::instance($data->courseid);
        if (has_capability('moodle/course:update', $context)) {
            $conditions = array('id' => $data->courseid);
            if (!$course = $DB->get_record('course', $conditions)) {
                error('Course ID was incorrect');
            } else {
                //// process making grades available data
                $course->showgrades = $data->grades;
                //// Process course availability
                $course->visible = $data->course;
                if (!$DB->update_record('course', $course)) {
                    print_error('coursenotupdated');
                }
            }

            rebuild_course_cache($courseid, true);
        }
    }

    // Silence debug output
    $PAGE->set_url('/');

redirect($returnurl);
