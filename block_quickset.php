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
 * A block that allows for quickly changing some common course settings.
 * 
 * @package     block_quickset
 * @copyright   2012 Bob Puffer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_quickset extends block_base {

    /**
     * The option chosen to allow students access.
     */
    const AVAILABLE = 1;
    /**
     * The option chose to deny students access.
     */
    const UNAVAILABLE = 0;
    
    function init() {
        $this->title = get_string('pluginname', 'block_quickset');
        $this->cron = 1;
    }

//init  
    // only one instance of this block is required
    function instance_allow_multiple() {
        return false;
    }

//instance_allow_multiple
    // label and button values can be set in admin
    function has_config() {
        return false;
    }

    /**
     * 
     * Gets the HTML output for the block
     * 
     * @return string
     */
    function get_content() {
        global $CFG, $COURSE, $DB;
        //Copy values to variables for outputting
        //heredoc style can't parse constants
        $available = self::AVAILABLE;
        $unavailable = self::UNAVAILABLE;

        $this->content = new stdClass;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (has_capability('moodle/course:update', $context)) {
            if ($COURSE->visible == 1) {
                $students = 'green';
                $studentschecked = ' checked="checked"';
                $studentsunchecked = '';
            } else {
                $students = 'red';
                $studentsunchecked = ' checked="checked"';
                $studentschecked = '';
            }
            if ($COURSE->showgrades == 1) {
                $grades = 'green';
                $gradeschecked = ' checked="checked"';
                $gradesunchecked = '';
            } else {
                $grades = 'red';
                $gradesunchecked = ' checked="checked"';
                $gradeschecked = '';
            }

            $format = course_get_format($COURSE);
            $format_options = $format->get_format_options();
            $sessionkey = sesskey();
            $this->content->text = <<<EOD
<form action="{$CFG->wwwroot}/blocks/quickset/processform.php" method="post">
<input type="hidden" value="{$COURSE->id}" name="courseid" />
<input type="hidden" value="{$sessionkey}" name="sesskey" />
<input type="hidden" value="grader" name="report"/>
EOD;

            //Get localized strings for output
            $strings = array(
                'yes' => get_string('yes', 'block_quickset'),
                'no' => get_string('no', 'block_quickset'),
                'classvisible' => get_string('classvisible', 'block_quickset'),
                'gradesvisible' => get_string('gradesvisible', 'block_quickset'),
                'sectionsvisible' => get_string('sectionsvisible', 'block_quickset'),
                'updatesettings' => get_string('updatesettings', 'block_quickset'),
                'moresettings' => get_string('moresettings', 'block_quickset'),
                'note' => get_string('note', 'block_quickset'),
                'editsections' => get_string('editsections', 'block_quickset')
            );
            
            $this->content->text .= <<<EOD
    <div id="context">
        <div class="setright">
            <div class="heading">{$strings['yes']}</div>|<div class="heading">{$strings['no']}</div>
        </div>
        <div class="clearfix"></div>
        <div class="setleft {$students}">{$strings['classvisible']}</div>
        <div class="setright">
            <label>
                <input type="radio" style="margin:0;" name="course" value={$available} {$studentschecked} />
            </label>
            <label>
                <input type="radio" style="margin:0;" name="course" value={$unavailable} {$studentsunchecked} />
            </label>
        </div>

        <div class="setleft {$grades}">{$strings['gradesvisible']}</div>
        <div class="setright">
            <label>
                <input type="radio" style="margin:0;" name="grades" value={$available} {$gradeschecked} />
            </label>
            <label>
                <input type="radio" style="margin:0;" name="grades" value={$unavailable} {$gradesunchecked} />
            </label>
        </div>
EOD;

            if ($format->uses_sections()) {
                $this->content->text .= <<<EOD

        <div class="setleft blue" id="sections"><span class="valign">{$strings['sectionsvisible']}</span></div>
EOD;

                $this->content->text .=
                        '<div class="setright">'
                        . '<select name="number">';

                //Get the maximum number of sections from the database
                if (!$configvalue = $DB->get_record('config_plugins', array('name' => 'maxsections'), 'value')) {
                    //If the lookup failed for some reason, use the max of the default number of sections or the current number
                    $maxsections = max(52, $format_options['numsections']);
                } else {
                    $maxsections = $configvalue->value;
                }


                for ($i = 1; $maxsections >= $i; $i++) {
                    $this->content->text .= '<option value="' . $i . '" ';
                    if ($format_options['numsections'] == $i) {
                        $this->content->text .= 'selected';
                    }
                    $this->content->text .= '>     ' . $i . '</option>';
                }

                $this->content->text .=
                        '</select>'
                        . '</div>';
            }

            //Construct moodle_url with hidden params (so that I don't need to use a form and POST data; I can use a regular link instead)
            $editurl = new moodle_url(
                            $CFG->wwwroot . '/blocks/quickset/edit.php', 
                            array(
                                'courseid' => $COURSE->id, 
                                'sesskey' => $sessionkey,
                                'pageurl' => $CFG->wwwroot .'/course/view.php?id=' . $COURSE->id
                                )
                        );
            
            $this->content->text .= <<<EOD
        <div class="submit clearfix">
        <div class="center">
            <input type="submit" value="{$strings['updatesettings']}"/ class="button"></div>
        </div></form>
        <div class="setleft" style="width:100%;">
            <a href="{$CFG->wwwroot}/course/edit.php?id={$COURSE->id}">{$strings['moresettings']}</a><br />
            <a href="{$editurl}">{$strings['editsections']}</a>
        </div>
    </div>
        <div class="small setleft">{$strings['note']}</div>
        <div class="clearfix"></div>
EOD;
        }
        //no footer, thanks
        $this->content->footer = '';
        return $this->content;
    }

//get_content

    function specialisation() {
        //empty!
    }

//specialisation
}

//block_course_settings