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
 * Class modspend_grade_table
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\tables;

use cm_info;
use coding_exception;
use context_course;
use dml_exception;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;
use theme_cbe\course;
use theme_cbe\output\module_component;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once('../../lib/tablelib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class modspend_grade_table
 *
 * @package    block_tresipuntmodspend
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modspend_grade_table extends table_sql {

    /** @var int User ID */
    protected $user_id;

    /** @var string Rolename */
    protected $rolename;

    /**
     * tasks_table constructor.
     *
     * @param int $user_id
     * @param string $rolename
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function __construct(int $user_id, string $rolename) {
        $uniqueid = time();
        parent::__construct($uniqueid);

        $this->user_id = $user_id;
        $this->rolename = $rolename;

        $this->pageable(true);
        $this->collapsible(true);
        $this->sortable(true);
        $moodle_url = new moodle_url('/theme/cbe/view_modspend.php', ['rolename' => $rolename]);
        $this->define_baseurl($moodle_url);

        $this->define_columns([
            'task', 'course', 'gradingduedate', 'grade', 'status'
        ]);

        $this->define_headers([
            get_string('task_table_task', 'theme_cbe'),
            get_string('course'),
            get_string('gradingduedate', 'mod_assign'),
            get_string('task_table_grade', 'theme_cbe'),
            get_string('status')
        ]);

        $this->is_downloadable(false);
        $this->is_collapsible = false;

        $this->sortable(true, 'gradingduedate', SORT_ASC);

        $this->column_style('course', 'text-align', 'left');
        $this->column_style('task', 'text-align', 'left');
        $this->column_style('gradingduedate', 'text-align', 'left');
    }

    /**
     * Query DB.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $this->rawdata = $this->get_data();
    }

    /**
     * Get Data
     *
     * @return array
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_data(): array {
        global $DB;

        $courses = enrol_get_all_users_courses($this->user_id, true);
        $courses_as = [];
        foreach ($courses as $course) {
            $coursecontext = context_course::instance($course->id);
            $roles = get_user_roles($coursecontext);
            foreach ($roles as $role) {
                if ($role->shortname === $this->rolename) {
                    $courses_as[] = $course;
                    break;
                }
            }
        }

        $data = [];
        foreach ($courses_as as $course) {
            $course_cbe = new course($course->id);
            $fastmodinfo = get_fast_modinfo($course);
            /** @var cm_info[] $cms */
            $cms = $fastmodinfo ? $fastmodinfo->get_cms() : [];

            foreach ($cms as $cm) {
                if ($cm->modname === 'assign' && $cm->uservisible) {
                    $instance = $DB->get_record(
                        'assign', array('id' => $cm->instance), '*', MUST_EXIST);

                    $status = new stdClass();
                    $students = $course_cbe->get_users_by_role('student');
                    $status->students = count($students);
                    $submitted = 0;
                    $pending_graded = 0;
                    foreach ($students as $student) {
                        $submission = $DB->get_record('assign_submission',
                            array('assignment' => $cm->instance, 'userid' => $student->id), '*');
                        if ($submission->status === 'submitted') {
                            $grades = assign_get_user_grades($instance, $student->id);
                            if (count($grades) === 0) {
                                $pending_graded ++;
                            } else {
                                $grade = current($grades);
                                if ($grade->usermodified < 0) {
                                    $pending_graded ++;
                                }
                            }
                            $submitted ++;
                        }
                    }

                    $status->submitted = $submitted;
                    $status->pending = $pending_graded;

                    $row = new stdClass();
                    $row->course = $cm->course;
                    $row->id = $cm->id;
                    $row->section = $cm->sectionnum;
                    $row->name = $cm->name;
                    $row->modname = $cm->modname;
                    $row->status = $status;
                    $row->gradingduedate = $instance->gradingduedate;

                    if ($pending_graded > 0) {
                        $data[] = $row;
                    }
                }
            }
        }

        usort($data, function($a, $b) {
            return $a->gradingduedate > $b->gradingduedate ? 1 : -1;
        });

        return $data;
    }

    /**
     * Col course
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws moodle_exception
     */
    public function col_course(stdClass $row): string {
        $course = get_course($row->course);
        $view_url = new moodle_url('/theme/cbe/view_board.php', ['id'=> $course->id]);
        return '<a href="' . $view_url . '" target="_blank">' . $course->shortname . '</a>';
    }

    /**
     * Col task
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_task(stdClass $row): string {
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $page = new module_component($row->id);

        return $output->render($page);
    }

    /**
     * Col gradingduedate
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     */
    public function col_gradingduedate(stdClass $row): string {
        return !empty($row->gradingduedate) ? userdate(
            $row->gradingduedate, get_string('strftimedaydatetime', 'core_langconfig')) : '-';
    }

    /**
     * Col grade
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception|moodle_exception
     */
    public function col_grade(stdClass $row): string {
        $view_url = new moodle_url('/mod/assign/view.php', ['id'=> $row->id, 'action' => 'grading']);
        return '<a href="' . $view_url . '" target="_blank">'
            . get_string('course_left_grades', 'theme_cbe')
            . '</a>';
    }

    /**
     * Col status
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     */
    public function col_status(stdClass $row): string {
        if (!empty($row->status->pending)) {
            return get_string('modspend_pending', 'theme_cbe', $row->status->pending);
        } else {
            return '-';
        }
    }

}
