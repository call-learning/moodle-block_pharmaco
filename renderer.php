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

defined('MOODLE_INTERNAL') || die();

class block_myoverview_enva_renderer extends plugin_renderer_base {
    
    public function render_test_course_prompt(test_course_prompt $tp) {
        return $this->render_from_template('block_myoverview_enva/complete-course', $tp->export_for_template($this));
    }
    
    public function render_ordered_course_list(ordered_course_list $cl) {
        return $this->render_from_template('block_myoverview_enva/course-list', $cl->export_for_template($this));
    }
}

class test_course_prompt implements renderable, templatable {
    public $userid;
    public $course;
    
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($userid, $course) {
        $this->userid = $userid;
        $this->course = $course;
    }
    
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        
        $percentage = \core_completion\progress::get_course_progress_percentage($this->course, $this->userid);
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        }
        
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        }
        $courselink = new action_link(
            new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->course->id)),
            ($percentage == 0) ? get_string('launchcourse:first', 'block_myoverview_enva') : get_string('launchcourse:started', 'block_myoverview_enva'));
        
        $contextdata = [
            'fullname' => $this->course->fullname,
            'progress' => $percentage,
            'viewurl' => new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->course->id)),
            'hasstarted' => $percentage > 0
        ];
        $contextdata += (array)$courselink->export_for_template($output);
        return $contextdata;
    }
}


class ordered_course_list implements renderable, templatable {
    public $userid;
    public $reviewthreshold;
    
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($userid, $reviewthreshold) {
        $this->userid = $userid;
        $this->reviewthreshold = $reviewthreshold;
    }
    
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        
        $ts = new \local_enva\tag_scores(\local_enva\helper::get_test_course_id(), $this->userid);
        $cl = new \local_enva\course_list_by_score($this->userid, $ts->compute());
        $courselist = $cl->get_list();
        
        // Make sure we add the necessary information for the template in the list
        $courselistarray = [];
        
        $selectioncourseid = \local_enva\helper::get_test_course_id(); // We remove the test course from the list
        // and add it at the end
        $selectioncourse = null;
        foreach ($courselist as $c) {
            if ($c->id != $selectioncourseid) {
                $this->set_course_data($c, $c->progress > 0, $c->score);
                // TODO Check if equal or equal/less than equal
                $courselistarray[] = (array)$c;
            } else {
                $this->set_course_data($c); // Not to be reviewed
                $selectioncourse = $c;
            }
        }
        $courselistarray[] = $selectioncourse; // Add test course at the end
        return array('courses' => $courselistarray);
    }
    
    protected function set_course_data(&$c, $hasstarted=true, $score=null, $hide=false) {
        global $CFG;
        $c->viewurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $c->id));
        $c->hasstarted = $hasstarted;
        $c->tobereviewed = false;
        $c->score = 1.0;
        if (!is_null($score)) {
            $c->tobereviewed = ($score < $this->reviewthreshold);
            $c->score = $score;
        }
        $c->summary = html_to_text(format_text($c->summary ,$c->format ));
        $c->progress = is_null($c->progress)?0:$c->progress;
        if ($hide) {
            $c->visible = false;
        }
    }
}