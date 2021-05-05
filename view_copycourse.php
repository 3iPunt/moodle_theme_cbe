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
 * View copy course.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_cbe\forms\copycourse_form;
use theme_cbe\output\copycourse_page;

require_once('../../config.php');

global $PAGE, $OUTPUT;

require_login();

// Course_module ID, or
$id = required_param('id', PARAM_INT);

require_course_login($id);

$title = get_string('copy_course', 'theme_cbe');

$contextsystem = context_system::instance();
require_capability('moodle/course:create', $contextsystem);

$course = get_course($id);
$url = new moodle_url('/theme/cbe/view_copycourse.php', array('id' => $id));

if (isset($course)) {

    require_capability('moodle/course:update', context_course::instance($id));

    $PAGE->set_context(context_course::instance($id));
    $PAGE->set_title($course->fullname . ': '. $title);
    $PAGE->set_heading($title);
    $PAGE->set_url($url);

    $returnto = '';
    $returnurl = '';

    // Get data ready for mform.
    $mform = new copycourse_form(
        $url,
        array('course' => $course, 'returnto' => $returnto, 'returnurl' => $returnurl));

    if ($mform->is_cancelled()) {
        // The form has been cancelled, take them back to what ever the return to is.
        redirect($returnurl);

    } else if ($mdata = $mform->get_data()) {

        // Process the form and create the copy task.
        $backupcopy = new \core_backup\copy\copy($mdata);
        $backupcopy->create_copy();

        if (!empty($mdata->submitdisplay)) {
            // Redirect to the copy progress overview.
            $progressurl = new moodle_url('/theme/cbe/view_copycourse_progress.php', array('id' => $course->id));
            redirect($progressurl);
        } else {
            // Redirect to the course view page.
            $coursesurl = new moodle_url('/course/view.php', array('id' => $course->id));
            redirect($coursesurl);
        }

    } else {
        $output = $PAGE->get_renderer('theme_cbe');
        echo $OUTPUT->header();
        $page = new copycourse_page($id, $mform);
        echo $output->render($page);
        echo $OUTPUT->footer();
    }
}
