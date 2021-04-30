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
 * @package     theme_cbe
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

namespace theme_cbe\external;

use core_course_category;
use core_user_external;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use moodle_exception;
use moodle_url;
use theme_cbe\user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class course_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function create_course_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'fullname' => new external_value(PARAM_TEXT, 'Course Fullname', true),
                'shortname' => new external_value(PARAM_TEXT, 'Course Shortname', true),
                'category' => new external_value(PARAM_INT, 'Category ID', true),
                'visible' => new external_value(PARAM_BOOL, 'Visibility', true),
            )
        );
    }

    /**
     * @param string $fullname
     * @param string $shortname
     * @param int $category
     * @param bool $visible
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function create_course(string $fullname, string $shortname, int $category, bool $visible): array {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/user/externallib.php');
        self::validate_parameters(
            self::create_course_parameters(), [
                'fullname' => $fullname,
                'shortname' => $shortname,
                'category' => $category,
                'visible' => $visible
            ]
        );

        $redirect = '';

        // Validamos que el usuario puede crear cursos.
        if (user::can_create_courses()) {
            $category = core_course_category::get($category);
            if ($category) {
                if (core_course_category::can_view_category($category)) {
                    $datacourse = new \stdClass();
                    $datacourse->category = $category->id;
                    $datacourse->shortname = $shortname;
                    $datacourse->fullname = $fullname;
                    $datacourse->visible = $visible;
                    $course = create_course($datacourse);
                    // Hidden to user in dashboard:
                    if (!$visible) {
                        $prop = 'block_myoverview_hidden_course_' . $course->id;
                        $props = array(array('type' => $prop, 'value' => true));
                        core_user_external::update_user_preferences(
                            $USER->id,
                            null,
                            $props);
                    }
                    // Enrol teacher.
                    $plugin_instance = $DB->get_record("enrol",
                        array('courseid' => $course->id, 'enrol'=>'manual'));
                    $plugin = enrol_get_plugin('manual');
                    $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
                    $plugin->enrol_user($plugin_instance, $USER->id, $roleid);
                    // Response
                    $success = true;
                    $error = '';
                    $view_url = new moodle_url('/theme/cbe/view_board.php', ['id'=> $course->id]);
                    $redirect = $view_url->out(false);
                } else {
                    $success = false;
                    $error = 'El usuario no puede crear cursos en esta categoría';
                }
            } else {
                $success = false;
                $error = 'La categoría no existe';
            }
        } else {
            $success = false;
            $error = 'El usuario no tiene permisos para crear cursos';
        }

        return [
            'success' => $success,
            'error' => $error,
            'redirect' => $redirect
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function create_course_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
                'redirect' => new external_value(PARAM_TEXT, 'URL Course view board redirect'),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function check_course_shortname_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'shortname' => new external_value(PARAM_TEXT, 'Course Shortname', true)
            )
        );
    }

    /**
     * @param string $shortname
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function check_course_shortname(string $shortname): array {
        global $DB;

        self::validate_parameters(
            self::check_course_shortname_parameters(), [
                'shortname' => $shortname
            ]
        );

        $course = $DB->get_record(
            'course', array('shortname' => $shortname), 'fullname', IGNORE_MULTIPLE);

        if ($course) {
            $exist = true;
            $msg = get_string('shortnametaken', '', $course->fullname);
        } else {
            $exist = false;
            $msg = null;
        }

        return [
            'success' => true,
            'msg' => $msg,
            'exist' => $exist
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function check_course_shortname_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'msg' => new external_value(PARAM_TEXT, 'Message'),
                'exist' => new external_value(PARAM_BOOL, 'Course with this shortname exist?')
            )
        );
    }

}
