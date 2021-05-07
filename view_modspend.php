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
 * View Modules Pending.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use theme_cbe\output\modspend_page;

require_once('../../config.php');

global $PAGE, $OUTPUT;

// Rolename
$rolename = required_param('rolename', PARAM_TEXT);

require_login();

if ($rolename === 'editingteacher' || $rolename === 'student') {
    $title = get_string($rolename . '_button', 'theme_cbe');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    $PAGE->set_url('/theme/cbe/view_modspend.php', ['rolename' => $rolename]);
    $output = $PAGE->get_renderer('theme_cbe');
    echo $OUTPUT->header();
    $page = new modspend_page($rolename);
    echo $output->render($page);
    echo $OUTPUT->footer();
} else {
    print_error(get_string('role_bad', 'theme_cbe'));
}
