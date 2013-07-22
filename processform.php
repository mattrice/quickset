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
    global $CFG, $DB;
    require_once($CFG->dirroot . '/lib/accesslib.php');
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/course/format/lib.php');
    $courseid = required_param('courseid', PARAM_INT);
    $returnurl = $_SERVER['HTTP_REFERER'];

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if ($data = data_submitted() and confirm_sesskey()) {
        $context = get_context_instance(CONTEXT_COURSE, $data->courseid);
        if (has_capability('moodle/course:update', $context)) {
            $conditions = array('id' => $data->courseid);
            if (!$course = $DB->get_record('course', $conditions)) {
                error('Course ID was incorrect');
            } else {
                //// process making grades available data
                $course->showgrades = $data->grades;
                //// Process course availability
                $course->visible = $data->course;
                $course->fullname = addslashes($course->fullname);
                if (!$DB->update_record('course', $course)) {
                    print_error('coursenotupdated');
                }
            }
            //// Process number of sections
            //Get the maximum number of sections from the database
            if (!$configvalue = $DB->get_record('config_plugins', array('name' => 'maxsections'), 'value')) {
                //If the lookup failed for some reason, use the default number of sections
                $maxsections = 52;
            } else {
                $maxsections = $configvalue->value;
            }
            $numsections = min($data->number, $maxsections);
            $format = course_get_format($data->courseid);
            if ($format->uses_sections()) {
                $formatparams = array('numsections' => $numsections);
                $format->update_course_format_options($formatparams);
            }
            rebuild_course_cache($courseid, true);
        }
    }

redirect($returnurl);