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
 * Class tasks_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\tables;

use cm_info;
use coding_exception;
use core_course\search\section;
use dml_exception;
use theme_cbe\course_user;
use theme_cbe\output\module_component;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once('../../lib/tablelib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class tasks_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tasks_table extends table_sql {

    /** @var int Course ID */
    protected $course_id;

    /** @var int User ID */
    protected $user_id;

    /**
     * students_in_course_table constructor.
     *
     * @param int $course_id
     * @param int $user_id
     * @throws moodle_exception
     */
    public function __construct(int $course_id, int $user_id) {
        $uniqueid = time();
        parent::__construct($uniqueid);

        $this->course_id = $course_id;
        $this->user_id = $user_id;

        $this->pageable(true);
        $this->collapsible(true);
        $this->sortable(true);
        $url = '/theme/cbe/view_tasks.php';
        $params_url = ['id' => $course_id];
        $moodle_url = new moodle_url($url, $params_url);
        $this->define_baseurl($moodle_url);

        $this->define_columns([
            'task', 'remaining', 'duedate', 'state', 'grade'
        ]);
        $this->define_headers([
            get_string('task_table_task', 'theme_cbe'),
            get_string('task_table_remaining', 'theme_cbe'),
            get_string('task_table_duedate', 'theme_cbe'),
            get_string('task_table_state', 'theme_cbe'),
            get_string('task_table_grade', 'theme_cbe')
        ]);

        $this->is_downloadable(false);
        $this->is_collapsible = false;

        $this->sortable(true, 'duedate');

        $this->column_style('task', 'text-align', 'left');
        $this->column_style('remaining', 'text-align', 'left');
        $this->column_style('duedate', 'text-align', 'left');
        $this->column_style('state', 'text-align', 'left');
        $this->column_style('grade', 'text-align', 'center');
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
        $course = get_course($this->course_id);
        $data = [];

        $fastmodinfo = get_fast_modinfo($course);
        /** @var cm_info[] $cms */
        $cms = $fastmodinfo ? $fastmodinfo->get_cms() : [];

        foreach ($cms as $cm) {
            if ($cm->modname === 'assign') {
                $row = new stdClass();
                $row->id = $cm->id;
                $row->instance = $cm->instance;
                $row->section = $cm->sectionnum;
                $row->name = $cm->name;
                $row->modname = $cm->modname;
                $instance = $DB->get_record(
                    'assign', array('id' => $cm->instance), '*', MUST_EXIST);
                $grades = assign_get_user_grades($instance, $this->user_id);
                $grade = reset($grades);

                if (!$grade || !$grade->rawgrade || $grade->rawgrade < 0) {
                    $row->rawgrade = '';
                } else {
                    $row->rawgrade = round($grade->rawgrade, 2);
                }

                $row->duedate = $instance->duedate;
                $data[] = $row;
            }
        }

        usort($data, function($a, $b) {
            return $a->duedate < $b->duedate ? 1 : -1;
        });

        return $data;
    }

    /**
     * Col section
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_section(stdClass $row): string {
        return get_section_name($this->course_id, $row->section);
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
     * Col remaining time
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_remaining(stdClass $row): string {
        $now = time();
        if ($now < $row->duedate) {
            $data = format_time($row->duedate - $now);
        } else {
            $data = '-';
        }
        return $data;
    }

    /**
     * Col duedate
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     */
    public function col_duedate(stdClass $row): string {
        return !empty($row->duedate) ? userdate(
            $row->duedate, get_string('strftimedaydate', 'core_langconfig')) : '-';
    }

    /**
     * Col grade
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_grade(stdClass $row): string {
        if (course_user::is_teacher($this->course_id)) {
            $view_url = new moodle_url('/mod/assign/view.php', ['id'=> $row->id, 'action' => 'grading']);
            return '<a href="' . $view_url . '" target="_blank">'
                . get_string('course_left_grades', 'theme_cbe')
                . '</a>';
        } else {
            return $row->rawgrade === '' ? '-' : '<strong>' . $row->rawgrade . '</strong>';
        }

    }

    /**
     * Col state
     *
     * @param stdClass $row
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function col_state(stdClass $row): string {
        global $DB;

        if (course_user::is_teacher($this->course_id)) {
            if (time() > $row->duedate ) {
                $state = get_string('task_submit_duedate_out', 'theme_cbe');
            } else {
                $state = get_string('task_submit_duedate_in', 'theme_cbe');
            }
            return $state;
        } else {
            if ($row->rawgrade) return get_string('graded', 'assign') .
                '<i class="fa fa-pencil graded" aria-hidden="true"></i>';

            $submission = $DB->get_record('assign_submission',
                array('assignment' => $row->instance, 'userid' => $this->user_id), '*');

            $state = '-';

            if ($submission->status === 'submitted') {
                $state = get_string('submissionstatus_submitted', 'assign') .
                '<i class="fa fa-archive submitted" aria-hidden="true"></i>';
            }

            if (time() > $row->duedate && $submission->status !== 'submitted') {
                $state = get_string('task_submit_duedate_out', 'theme_cbe') .
                    '<i class="fa fa-calendar-times-o out" aria-hidden="true"></i>';;
            } else if (time() < $row->duedate && $submission->status !== 'submitted') {
                if ($submission->status === 'draft') {
                    $state = get_string('submissionstatus_draft', 'assign') .
                        '<i class="fa fa-eraser draft" aria-hidden="true"></i>';
                } else if ($submission->status === 'reopened') {
                    $state = get_string('submissionstatus_reopened', 'theme_cbe') .
                        '<i class="fa fa-folder-open reopened" aria-hidden="true"></i>';;
                }   else {
                    $state = get_string('task_not_delivery', 'theme_cbe') .
                        '<i class="fa fa-times not_delivery" aria-hidden="true"></i>';;
                }
            }
            return $state;
        }
    }
}
