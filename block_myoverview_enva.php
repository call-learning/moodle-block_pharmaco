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
 * Block myoverview_enva is defined here.
 *
 * @package     block_myoverview_enva
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/myoverview/block_myoverview.php');
require_once($CFG->libdir . '/completionlib.php');
/**
 * myoverview_enva block.
 *
 * @package    block_myoverview_enva
 * @copyright  2018 Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myoverview_enva extends block_myoverview {
    /**
     * Default threshold to display a course a to be reviewed or not
     */
    public static $DEFAULT_REVIEW_THRESHOLD = 0.5;
    
    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_myoverview_enva');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $USER, $CFG, $DB;
        if ($this->content !== null) {
            return $this->content;
        }
        
        $is_external = \local_enva\helper::is_user_external_role($USER->id);
        if (!$is_external) {
            return parent::get_content();
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
    
        // Here we either display the link to the test course or the list of course ordered by their scores
        
        // Check first if we have completed the test course
        $courseid = \local_enva\helper::get_test_course_id();
        $course = $DB->get_record('course', array('id'=>$courseid));
        
        $ccompletion = new completion_completion(array('course' => $courseid, 'userid' => $USER->id));
        $renderer = $this->page->get_renderer('block_myoverview_enva');
        
        
        if (!$ccompletion->is_complete()) {
            $tcp = new test_course_prompt($USER->id, $course);
            $this->content->text =  $renderer->render($tcp);
        } else {
            $reviewthreshold = block_myoverview_enva::$DEFAULT_REVIEW_THRESHOLD;
            $config = get_config('block_myoverview_enva');
            if (isset($config->review_threshold)) {
                $reviewthreshold = $config->review_threshold;
            }
            $ocl = new ordered_course_list($USER->id, $reviewthreshold);
            $this->content->text =  $renderer->render($ocl);
        }
        
        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediatly after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_myoverview');
            // We show the parent's title
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    function has_config() {
        return true;
    }
}
