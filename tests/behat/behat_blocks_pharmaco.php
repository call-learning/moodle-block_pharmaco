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
 * Block pharmaco generator
 *
 * @package     block_pharmaco
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Block pharmaco behat routines
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_blocks_pharmaco extends behat_base {
    /**
     * Set the given course to be the entry course
     *
     * @Given /^I set the entry course to "(?P<course_shortname>(?:[^"]|\\")*)"$/
     */
    public function i_set_the_entry_course_to($courseshortname) {
        global $DB;

        $id = $DB->get_field('course', 'id', array('shortname' => $courseshortname));
        if ($id) {
            set_config('coursetocomplete', $id, 'local_pharmaco');
        }
    }
}