<?php

	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
    require_once('../../config.php');
	global $CFG, $DB;
	global $COURSE;
	global $USER;
    require_once($CFG->dirroot.'/lib/accesslib.php');
	$courseid = required_param('courseid',PARAM_INT);
	$returnurl = $_SERVER['HTTP_REFERER'];

	$shortname = $COURSE->shortname;

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
				//// Process number of sections
				$course->numsections = min($data->number,52);
				$course->fullname = addslashes($course->fullname);
				if (!$DB->update_record('course',$course)) {
					print_error('coursenotupdated');
				}
			}
			//// Toggle AJAX for user
			$conditions = array('id' => $USER->id);
			if (!$user = $DB->get_record('user', $conditions)) {
				error('User ID was incorrect');
			}
			$user->ajax = $data->ajax;
			if (!$DB->update_record('user', $user)) {
				echo 'not updated';
				print_error('usernotupdated');
			}
			$USER->ajax = $user->ajax;
		}
    }
    redirect($returnurl);
?>