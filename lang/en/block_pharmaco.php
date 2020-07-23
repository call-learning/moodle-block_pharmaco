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
 * Plugin strings are defined here.
 *
 * @package     block_pharmaco
 * @category    string
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Pharmaco';
$string['blocktitle'] = 'Pharmacovigilance';
$string['testcourseneedscompletion'] = 'The course \'{$a}\' needs to be completed in order to be registered
 to the full program.';
$string['progressmessage'] = '{$a}% completed';
$string['launchcourse:started'] = 'Continue';
$string['launchcourse:first'] = 'Start';

// Settings.
$string['reviewthreshold'] = 'Grade review threshold';
$string['reviewthreshold:help'] = 'Threshold (between 0 and 1) to mark a course a to be reviewed';

// Other.
$string['courseicon'] = 'Course Icon';