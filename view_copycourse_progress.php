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
 * View copy course progress.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_cbe\output\copycourse_progress_page;

require_once('../../config.php');

global $CFG, $PAGE, $OUTPUT, $USER;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('id', PARAM_INT);

$url = new moodle_url('/theme/cbe/view_copycourse_progress.php', array('id' => $courseid));
$course = get_course($courseid);
$coursecontext = context_course::instance($course->id);

// Security and access checks.
require_login($course, false);
$copycaps = array('moodle/backup:backupcourse', 'moodle/restore:restorecourse', 'moodle/course:create');
require_all_capabilities($copycaps, $coursecontext);

// Setup the page.
$title = get_string('copyprogresstitle', 'backup');
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->requires->js_call_amd('core_backup/async_backup', 'asyncCopyAllStatus');

// Build the page output.
echo $OUTPUT->header();
$output = $PAGE->get_renderer('theme_cbe');
$page = new copycourse_progress_page($courseid);
echo $output->render($page);
echo $OUTPUT->footer();







