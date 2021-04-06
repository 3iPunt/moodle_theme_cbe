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

use coding_exception;
use context_course;
use course_enrolment_manager;
use dml_exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use moodle_exception;
use moodle_url;
use theme_cbe\course;
use theme_cbe\course_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class section_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function sectioncreate_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'course_id' => new external_value(PARAM_INT, 'Course ID'),
                'name' => new external_value(PARAM_RAW, 'Section Name'),
                'position' => new external_value(PARAM_INT, 'Position'),
            ]
        );
    }

    /**
     * @param string $course_id
     * @param string $name
     * @param string $position
     * @return array
     * @throws invalid_parameter_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function sectioncreate(string $course_id, string $name, string $position): array {

        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::sectioncreate_parameters(), [
                'course_id' => $course_id,
                'name' => $name,
                'position' => $position
            ]
        );

        // Is teacher in the course?
        $isteacher = course_user::is_teacher($course_id);

        if ($isteacher) {

            $newsection = course_create_section($course_id, $position);
            if ($name !== '') {
                course_update_section($course_id, $newsection, array('name' => $name));
            }
            $success = true;
            $error = '';
        } else {
            $success = false;
            $error = 'El usuario no es profesor del curso';
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function sectioncreate_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }


}