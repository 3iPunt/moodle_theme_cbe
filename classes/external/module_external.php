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

class module_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function publication_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'Course ID'),
                'comment' => new external_value(PARAM_RAW, 'Teacher comment'),
                'mode' => new external_value(PARAM_TEXT, 'Mode: {all, student, group}'),
                'item' => new external_value(PARAM_INT, 'Item ID: {student ID, group ID}'),
            )
        );
    }

    /**
     * @param string $course_id
     * @param string $comment
     * @param string $mode
     * @param string $item
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function publication(string $course_id, string $comment, string $mode, string $item): array {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/course/lib.php');

        self::validate_parameters(
            self::publication_parameters(), [
                'course_id' => $course_id,
                'comment' => $comment,
                'mode' => $mode,
                'item' => $item
            ]
        );

        $course = get_course($course_id);

        // Is teacher in the course?
        $isteacher_in_course = false;
        $context_course = context_course::instance($course_id);
        $roles = get_user_roles($context_course, $USER->id);
        foreach ($roles as $role) {
            if ($role->shortname = 'teacher') {
                $isteacher_in_course = true;
            }
        }

        if ($isteacher_in_course) {
            $comment_name = preg_replace("/[\r\n|\n|\r]+/", " ", $comment);
            $name = fullname($USER) . ': ' . substr(trim($comment_name), 0, 200);
            $draftid_editor = file_get_submitted_draft_itemid('introeditor');

            $moduleinfo = new stdClass();
            $moduleinfo->modulename = 'tresipuntshare';
            $moduleinfo->section = 0;
            $moduleinfo->course = $course_id;
            $moduleinfo->teacher = $USER->id;
            $moduleinfo->name = $name;
            $moduleinfo->intro = $comment;
            $moduleinfo->introeditor = array('text'=> $comment, 'format'=> FORMAT_HTML, 'itemid'=>$draftid_editor);;
            $moduleinfo->visible = true;
            $cm = create_module($moduleinfo);

            $has_availability = false;

            if ($mode === 'student') {
                $user = core_user::get_user($item);
                $email = $user->email;
                if (!empty($email)) {
                    $availability = '{"op":"&","c":[{"type":"profile","sf":"email","op":"isequalto","v":"' . $email .'"}],"showc":[true]}';
                    $has_availability = true;
                }
            } else if ($mode === 'group') {
                $group = groups_get_group($item);
                if (isset($group)) {
                    $availability = '{"op":"&","c":[{"type":"group","id":'. $group->id .'}],"showc":[true]}';
                    $has_availability = true;
                }
            }

            if ($has_availability) {
                $tree = new \core_availability\tree(json_decode($availability));
                $tree->is_empty();
                $cm->id = $cm->coursemodule;
                $cm->availability = $availability;
                $DB->update_record('course_modules', $cm);
                course_modinfo::clear_instance_cache($course);
                rebuild_course_cache($course->id);
            }



            $success = true;
            $error = '';
        } else {
            $success = false;
            $error = 'El usuario no puede crear comentarios.';
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function publication_returns(): external_single_structure {
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
        $url = '';

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
                $newcom = $manager_comment->add(trim($comment));
                $success = true;
                $url = new moodle_url('/' . course_navigation::PAGE_BOARD,
                    ['id'=> $publication->get_course()->id, 'pub' => $cmid ], 'comm-' . $newcom->id);
                $url = $url->out(false);
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
            'url' => $url,
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
                'url' => new external_value(PARAM_URL, 'URL board with publication expand'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }




    /**
     * @return external_function_parameters
     */
    public static function course_module_delete_parameters(): external_function_parameters {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * @param string $cmid
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function course_module_delete(string $cmid): array {
        global $USER;

        self::validate_parameters(
            self::course_module_delete_parameters(), [
                'cmid' => $cmid
            ]
        );

        list($course, $cm) = get_course_and_cm_from_cmid($cmid);

        // Is teacher in the course?
        $isteacher_in_course = false;
        $context_course = context_course::instance($course->id);
        $roles = get_user_roles($context_course, $USER->id);
        foreach ($roles as $role) {
            if ($role->shortname = 'teacher') {
                $isteacher_in_course = true;
            }
        }

        if ($isteacher_in_course) {
            try {
                course_delete_module($cmid);
                $success = true;
                $error = '';
            } catch (moodle_exception $e) {
                $success = false;
                $error = $e->getMessage();
            }
        } else {
            $success = false;
            $error = 'El usuario no puede borrar el módulo';
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function course_module_delete_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }
}
