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
 * View tasks.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_cbe\output\tasks_page;

require_once('../../config.php');

global $PAGE, $OUTPUT;

// Course_module ID, or
$id = required_param('id', PARAM_INT);

require_login($id);

$title = get_string('tasks_page', 'theme_cbe');

$course = get_course($id);

if (isset($course)) {
    $PAGE->set_context(context_course::instance($id));
    $PAGE->set_title($course->fullname . ': '. $title);
    $PAGE->set_heading($title);
    $PAGE->set_url('/theme/cbe/view_tasks.php', array('id' => $id, 'courseid' => $course->id ));
    $output = $PAGE->get_renderer('theme_cbe');
    echo $OUTPUT->header();
    $page = new tasks_page($id);
    echo $output->render($page);
    echo $OUTPUT->footer();
}





