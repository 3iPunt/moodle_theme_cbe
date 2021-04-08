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
use context_course;
use context_module;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use theme_cbe\publication;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class module_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function publication_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'Course ID'),
                'comment' => new external_value(PARAM_TEXT, 'Teacher comment'),
            )
        );
    }

    /**
     * @param string $course_id
     * @param string $comment
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function publication(string $course_id, string $comment): array {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::publication_parameters(), [
                'course_id' => $course_id,
                'comment' => $comment
            ]
        );

        $moduleinfo = new stdClass();
        $moduleinfo->modulename = 'tresipuntshare';
        $moduleinfo->section = 0;
        $moduleinfo->course = $course_id;
        $moduleinfo->teacher = $USER->id;
        $moduleinfo->name = trim($comment);
        $moduleinfo->visible = true;
        $cm = create_module($moduleinfo);

        return [
            'cmid' => $cm->id
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function publication_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'cmid' => new external_value(PARAM_INT, 'course module id'),
            )
        );
    }

    /**
     * @return external_function_parameters
     */
    public static function publication_delete_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID'),
            )
        );
    }

    /**
     * @param string $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function publication_delete(string $cmid): array {
        global $USER, $DB;

        self::validate_parameters(
            self::publication_delete_parameters(), [
                'cmid' => $cmid
            ]
        );

        $error = '';

        $publication = new publication($cmid);

        // Is teacher in the course?
        $isteacher_in_course = false;
        $context_course = context_course::instance($publication->get_course()->id);
        $roles = get_user_roles($context_course, $USER->id);
        foreach ($roles as $role) {
            if ($role->shortname = 'teacher') {
                $isteacher_in_course = true;
            }
        }

        if ($isteacher_in_course) {
            $teacher_creator = $publication->get_teacher();
            // User is creator of publication?
            if ($USER->id == $teacher_creator) {
                $success = true;
                try  {
                    course_delete_module($cmid);
                } catch (moodle_exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                $success = false;
                $error = 'Esta publicación no le pertenece';
            }
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
    public static function publication_delete_returns() {
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
    public static function publication_comment_send_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID'),
                'comment' => new external_value(PARAM_RAW, 'Comment')
            )
        );
    }

    /**
     * @param string $cmid
     * @param string $comment
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function publication_comment_send(string $cmid, string $comment): array {

        global $CFG, $PAGE;

        require_once($CFG->dirroot . '/comment/lib.php');

        self::validate_parameters(
            self::publication_comment_send_parameters(), [
                'cmid' => $cmid,
                'comment' => $comment
            ]
        );

        $PAGE->set_context(context_module::instance($cmid));

        $error = '';

        $publication = new publication($cmid);

        $can = can_access_course($publication->get_course());

        if ($can) {
            $args = new stdClass();
            $args->context = context_module::instance($cmid);
            $args->courseid = $publication->get_course()->id;
            $args->cm = $publication->get_cm();
            $args->area = 'tresipuntshare';
            $args->itemid = $cmid;
            $args->component = 'mod_tresipuntshare';
            $manager_comment = new comment($args);
            if ($manager_comment->can_post()) {
                $manager_comment->add(trim($comment));
                $success = true;
            } else {
                $success = false;
                $error = 'El usuario no puede crear comentarios.';
            }

        } else {
            $success = false;
            $error = 'El usuario no tiene acceso al curso';
        }
        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function publication_comment_send_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }
}
