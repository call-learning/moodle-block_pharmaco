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
 * @package     block_myoverview_enva
 * @category    string
 * @copyright   2018 Laurent David <laurent@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Vue d\'Ensemble des cours (ENVA)';
$string['testcourseneedscompletion'] = 'Le cours \'{$a}\' doit être complété afin d\'être inscrit au programme complet';
$string['progressmessage'] = '{$a}% complété';

$string['launchcourse:started'] = 'Continuer';
$string['launchcourse:first'] = 'Démarrer';

// Settings
$string['reviewthreshold'] = 'Seuil des notes pour révision';
$string['reviewthreshold:help'] = 'Seuil (entre 0 et 1) qui permet de distinguer les cours à revoir ou non';

// Other
$string['courseicon'] = 'Icône du cours';