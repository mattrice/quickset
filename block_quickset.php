<?php
  
  /* This is the global search shortcut block - a single query can be entered, and
  * the user will be redirected to the query page where they can enter more
  *  advanced queries, and view the results of their search. When searching from
  *  this block, the broadest possible selection of documents is searched.
  *  
  *
  *  Todo: make strings -> get_string()  
  * 
  * @package search
  * @subpackage search block
  * @author: Michael Champanis (mchampan), reengineered by Valery Fremaux 
  * @date: 2006 06 25
  */
     
  class block_quickset extends block_base {
    
    function init() {
      $this->title = get_string('pluginname', 'block_quickset');
      $this->cron = 1;
    } //init  
    
    // only one instance of this block is required
    function instance_allow_multiple() {
      return false;
    } //instance_allow_multiple
    
    // label and button values can be set in admin
    function has_config() {
      return false;
    } //has_config
      
    function get_content() {
	global $CFG, $COURSE, $USER;
        define ('AVAILABLE', 1);
        define ('UNAVAILABLE', 0);
	  $this->content = new stdClass;

      $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    if (has_capability('moodle/course:update', $context)) {
        if ($USER->ajax == 1) {
            $ajax = 'green';
            $ajaxchecked = ' checked="checked"';
            $ajaxunchecked = '';
        } else {
            $ajax = 'red';
            $ajaxunchecked = ' checked="checked"';
            $ajaxchecked = '';
        }
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
        $this->content->text = '<form action="' . $CFG->wwwroot . '/blocks/quickset/processform.php" method="post">'
                . '<input type="hidden" value="'.$COURSE->id.'" name="courseid" />'
                . '<input type="hidden" value="'.sesskey().'" name="sesskey" />'
                . '<input type="hidden" value="grader" name="report"/>';
        $this->content->text .= '<div id="context">'
                . '<div class="ynlabel">Yes | No</div>'
				
                . '<div class="setleft ' . $students . '">Students see course?</div> <div class="setright"><input type="radio" name="course" value=' . AVAILABLE . $studentschecked . ' />&nbsp;&nbsp;<input type="radio" name="course" value=' . UNAVAILABLE . $studentsunchecked . ' /></div>'
				
                . '<div class="setleft ' . $ajax . '">I\'m Using AJAX? </div> <div><input type="radio" name="ajax" value=' . AVAILABLE . $ajaxchecked . ' />&nbsp;&nbsp;<input type="radio" name="ajax" value=' . UNAVAILABLE . $ajaxunchecked . ' /></div>'
				
                . '<div class="setleft ' . $grades . '">Grades visible?</div> <div><input type="radio" name="grades" value=' . AVAILABLE . $gradeschecked . ' />&nbsp;&nbsp;<input type="radio" name="grades" value=' . UNAVAILABLE . $gradesunchecked . ' /></div>'
				
                . '<div class="setleft blue toplevel">Visible sections </div> <div class="setright"><input type="text" name="number" size="2" value="'.$COURSE->numsections.'"/></div>'
                . '<div align="center"><input type="submit" value="Update settings"/ class="button"></div><br>'
                . '<div class="setleft"><a href="' . $CFG->wwwroot . '/course/edit.php?id=' . $COURSE->id . '">&raquo; More Settings</a></div>'
                . '</div></form><br><br>';
        $this->content->text .= '<span class="small">Note: This block invisible to students</span>';

/*
        $this->content->text .= '<form class="course_settings" action="'.$CFG->wwwroot.'/blocks/course_settings/processform.php" id="setsections" method="get" name="form">
        
        <td><input type="submit" value="Change" /></td></tr></table></form>';

        $this->content->text .= '<form class="course_settings" action="'.$CFG->wwwroot.'/blocks/course_settings/makeavailable.php" id="available" method="get" name="form">
        <table><tr>
        <input type="hidden" name="courseid" value="'.$COURSE->id.'"/ >
        <img src="'.$CFG->wwwroot.'/blocks/course_settings/'.$availpic.'" /></td>
        <td><input type="submit" value="Change" /></td></tr></table></form>';

        $this->content->text .= '<form class="course_settings" action="'.$CFG->wwwroot.'/blocks/course_settings/setajax.php" id="setajax" method="get" name="ajax">
        <table><tr><td class="setleft">
        '.$ajaxtoggle.'</td>
        <td><input type="submit" value="Change" /></td></tr></table></form>';

        $this->content->text .= '<form class="course_settings" action="'.$CFG->wwwroot.'/blocks/course_settings/gradesavailable.php" id="available" method="get" name="form">
        <table><tr><td class="setleft">
        <input type="hidden" name="courseid" value="'.$COURSE->id.'"/ >
        <img src="'.$CFG->wwwroot.'/blocks/course_settings/'.$gradespic.'" /></td>
        <td><input type="submit" value="Change" /></td></tr></table></form>';

        $this->content->text .= '<div style="text-align:center"><a href="'.$CFG->wwwroot.'/course/edit.php?id='.$COURSE->id.'">More settings</a></div>';
*/
    }
		  //no footer, thanks
		  $this->content->footer = '';     
		  return $this->content;      
    } //get_content
    
    function specialisation() {
      //empty!
    } //specialisation
    
     
  } //block_course_settings
?>