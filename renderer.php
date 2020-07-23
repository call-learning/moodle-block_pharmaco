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
 * Block pharmaco renderer
 *
 * @package     block_pharmaco
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_completion\progress;
use local_pharmaco\course_list_by_score;
use local_pharmaco\helper;
use local_pharmaco\tag_scores;

defined('MOODLE_INTERNAL') || die();

/**
 * Main renderer
 *
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_pharmaco_renderer extends plugin_renderer_base {

    /**
     * Course complete
     *
     * @param test_course_prompt $tp
     * @return bool|string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function render_test_course_prompt(test_course_prompt $tp) {
        return $this->render_from_template('block_pharmaco/complete-course', $tp->export_for_template($this));
    }

    /**
     * Course list
     *
     * @param ordered_course_list $cl
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_ordered_course_list(ordered_course_list $cl) {
        return $this->render_from_template('block_pharmaco/course-list', $cl->export_for_template($this));
    }
}

/**
 * Class test_course_prompt
 *
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_course_prompt implements renderable, templatable {
    /**
     * @var int $userid  Identifier
     */
    public $userid;
    /**
     * @var object $course Course
     */
    public $course;

    /**
     * Constructor.
     *
     * @param int $userid
     * @param object $course
     */
    public function __construct($userid, $course) {
        $this->userid = $userid;
        $this->course = $course;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $percentage = progress::get_course_progress_percentage($this->course, $this->userid);
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        }

        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        }
        $courselink = new action_link(
            new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->course->id)),
            ($percentage == 0) ? get_string('launchcourse:first', 'block_pharmaco') :
                get_string('launchcourse:started', 'block_pharmaco'));

        $contextdata = [
            'fullname' => $this->course->fullname,
            'progress' => $percentage,
            'viewurl' => new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $this->course->id)),
            'hasstarted' => $percentage > 0
        ];
        $contextdata += (array) $courselink->export_for_template($output);
        return $contextdata;
    }
}

/**
 * Class ordered_course_list
 *
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ordered_course_list implements renderable, templatable {
    /**
     * @var $userid User Identifier
     */
    public $userid = null;
    /**
     * @var $reviewthreshold Review Threshold
     */
    public $reviewthreshold = null;

    /**
     * Constructor.
     *
     * @param int $userid
     * @param float $reviewthreshold
     */
    public function __construct($userid, $reviewthreshold) {
        $this->userid = $userid;
        $this->reviewthreshold = $reviewthreshold;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        $ts = new tag_scores(helper::get_test_course_id(), $this->userid);
        $cl = new course_list_by_score($this->userid, $ts->compute());
        $courselist = $cl->get_list();

        // Make sure we add the necessary information for the template in the list.
        $courselistarray = [];

        $selectioncourseid = helper::get_test_course_id(); // We remove the test course from the list.
        // and add it at the end.
        $selectioncourse = null;
        foreach ($courselist as $c) {
            if ($c->id != $selectioncourseid) {
                $this->set_course_data($c, $output, $c->progress > 0, $c->score);
                // TODO Check if equal or equal/less than equal.
                $courselistarray[] = (array) $c;
            } else {
                $this->set_course_data($c, $output); // Not to be reviewed.
                $selectioncourse = $c;
            }
        }
        $courselistarray[] = $selectioncourse; // Add test course at the end.
        return array('courses' => $courselistarray);
    }

    /**
     * Set course Data
     *
     * @param object $c
     * @param core_renderer $output
     * @param bool $hasstarted
     * @param null $score
     * @param bool $hide
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function set_course_data(&$c, $output, $hasstarted = true, $score = null, $hide = false) {
        global $CFG;
        $c->viewurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $c->id));
        $c->hasstarted = $hasstarted;
        $c->tobereviewed = false;
        $c->score = 1.0;
        if (!is_null($score)) {
            $c->tobereviewed = ($score < $this->reviewthreshold);
            $c->score = $score;
        }
        $c->summary = html_to_text(format_text($c->summary, $c->format));
        $c->progress = is_null($c->progress) ? 0 : $c->progress;
        if ($hide) {
            $c->visible = false;
        }
        $cil = new core_course_list_element($c);
        $coursefiles = $cil->get_course_overviewfiles();
        $iconsrc = '';
        if (empty($coursefiles)) {
            $pix = new pix_icon('default',
                get_string('courseicon', 'block_pharmaco'),
                'block_pharmaco',
                array('class' => 'courseicon')
            );
            $iconsrc = $pix->export_for_template($output);
        } else {
            $file = reset($coursefiles);
            $src = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                null,
                $file->get_filepath(),
                $file->get_filename(),
                false
            );
            $iconsrc = array(
                'attributes' => array(
                    array('name' => 'src', 'value' => $src->out()),
                    array('name' => 'title', 'value' => get_string('courseicon', 'block_pharmaco')),
                ),
                'extraclasses' => 'courseicon'
            );
        }
        $c->icon = $iconsrc;
    }
}