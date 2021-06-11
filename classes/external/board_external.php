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

use comment;
use context;
use context_course;
use context_module;
use core\event\course_module_deleted;
use core_user;
use core_user\search\user;
use course_modinfo;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use moodle_url;
use theme_cbe\course_user;
use theme_cbe\models\board;
use theme_cbe\navigation\course_navigation;
use theme_cbe\publication;
use moodle_exception;
use stdClass;
use tool_uploaduser\local\text_progress_tracker;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/group/lib.php');

class board_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function anchor_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * @param int $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function anchor(int $cmid): array {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::anchor_parameters(), [
                'cmid' => $cmid
            ]
        );

        try {
            list($course, $cm) = get_course_and_cm_from_cmid($cmid);
            if (course_user::is_teacher($course->id)) {
                $board = new board($course->id);
                $board->set_mod_anchor($cmid);
                $success = true;
                $error = '';
            } else {
                $success = false;
                $error = 'El usuario no tiene permisos para actualizar.';
            }
        } catch (moodle_exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function anchor_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function remove_anchor_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * @param int $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function remove_anchor(int $cmid): array {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::remove_anchor_parameters(), [
                'cmid' => $cmid
            ]
        );

        try {
            list($course, $cm) = get_course_and_cm_from_cmid($cmid);
            if (course_user::is_teacher($course->id)) {
                $board = new board($course->id);
                $board->remove_mod_anchor($cmid);
                $success = true;
                $error = '';
            } else {
                $success = false;
                $error = 'El usuario no tiene permisos para actualizar.';
            }
        } catch (moodle_exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function remove_anchor_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function visible_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * @param int $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function visible(int $cmid): array {
        global $CFG, $PAGE;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::visible_parameters(), [
                'cmid' => $cmid
            ]
        );

        try {
            list($course, $cm) = get_course_and_cm_from_cmid($cmid);
            $coursecontext = context_course::instance($course->id);
            $PAGE->set_context($coursecontext);
            if (course_user::is_teacher($course->id)) {
                $board = new board($course->id);
                $board->update_ordermodules($cmid, true);
                $success = true;
                $error = '';
            } else {
                $success = false;
                $error = 'El usuario no tiene permisos para actualizar.';
            }
        } catch (moodle_exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function visible_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function hidden_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * @param int $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function hidden(int $cmid): array {
        global $CFG, $PAGE;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::hidden_parameters(), [
                'cmid' => $cmid
            ]
        );

        try {
            list($course, $cm) = get_course_and_cm_from_cmid($cmid);
            $coursecontext = context_course::instance($course->id);
            $PAGE->set_context($coursecontext);
            if (course_user::is_teacher($course->id)) {
                $board = new board($course->id);
                $board->update_ordermodules($cmid, false);
                $success = true;
                $error = '';
            } else {
                $success = false;
                $error = 'El usuario no tiene permisos para actualizar.';
            }
        } catch (moodle_exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function hidden_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }


}
