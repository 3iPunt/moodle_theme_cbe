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
 * Class course
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use coding_exception;
use core_course\external\course_summary_exporter;
use core_course_category;
use course_enrolment_manager;
use course_modinfo;
use dml_exception;
use moodle_exception;
use moodle_url;
use section_info;
use stdClass;
use user_picture;

global $CFG;
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/modinfolib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class course
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course  {

    /** @var int Course ID */
    protected $course_id;

    /** @var stdClass Course */
    protected $course;

    /**
     * constructor.
     *
     * @param int $course_id
     * @throws dml_exception
     */
    public function __construct(int $course_id) {
        $this->course_id = $course_id;
        $this->course = get_course($this->course_id);
    }

    /**
     * Get Name.
     *
     * @return string
     * @throws dml_exception
     */
    function get_name(): string {
        return get_course($this->course_id)->fullname;
    }

    /**
     * Get Category.
     *
     * @return string
     * @throws dml_exception
     * @throws moodle_exception
     */
    function get_category(): string {
        $category_id = get_course($this->course_id)->category;
        $category = core_course_category::get($category_id);
        return $category->get_formatted_name();
    }

    /**
     * Get Courseimage.
     *
     * @return string
     * @throws dml_exception
     */
    function get_courseimage(): string {
        $course = get_course($this->course_id);
        return course_summary_exporter::get_course_image($course);;
    }

    /**
     * Get Teachers.
     *
     * @return array
     * @throws dml_exception
     * @throws coding_exception
     */
    public function get_teachers(): array {
        global $PAGE, $DB;
        $course = get_course($this->course_id);
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $enrolmanager = new course_enrolment_manager($PAGE, $course, $instancefilter = null, $role->id,
            $searchfilter = '', $groupfilter = 0, $statusfilter = -1);
        $teachers = $enrolmanager->get_users(
            'u.lastname', 'ASC', 0, 0
        );
        $data = [];
        foreach ($teachers as $teacher) {
            $userpicture = new user_picture($teacher);
            $userpicture->size = 1;
            $pictureurl = $userpicture->get_url($PAGE)->out(false);
            $row = new stdClass();
            $row->id = $teacher->id;
            $row->fullname = fullname($teacher);
            $row->picture = $pictureurl;
            // TODO: If user has been connected less than 30 minutes ago
            $row->is_connected = true;
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get Students.
     *
     * @return array
     * @throws dml_exception
     * @throws coding_exception
     */
    public function get_students(): array {
        global $PAGE, $DB;
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $enrolmanager = new course_enrolment_manager($PAGE, $this->course, $instancefilter = null, $role->id,
            $searchfilter = '', $groupfilter = 0, $statusfilter = -1);
        $students = $enrolmanager->get_users(
            'u.firstname', 'ASC', 0, 0
        );
        $data = [];
        foreach ($students as $student) {
            $userpicture = new user_picture($student);
            $userpicture->size = 1;
            $pictureurl = $userpicture->get_url($PAGE)->out(false);
            $row = new stdClass();
            $row->id = $student->id;
            $row->fullname = fullname($student);
            $row->picture = $pictureurl;
            // TODO: If user has been connected less than 30 minutes ago
            $row->is_connected = true;
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get Groups.
     *
     * @return array
     */
    public function get_groups(): array {
        $groups = groups_get_all_groups($this->course_id);
        $data = [];
        foreach ($groups as $group) {
            $row = new stdClass();
            $row->id = $group->id;
            $row->name = $group->name;
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get Themes.
     *
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_themes(): array {

        $param = optional_param('section', null, PARAM_INT);

        $course = get_course($this->course_id);
        /** @var course_modinfo $modinfo */
        $modinfo = get_fast_modinfo($course->id);
        $sections = $modinfo->get_section_info_all();
        $themes = array();
        foreach ($sections as $section) {
            if ($section->section > 0) {
                if (is_null($section->name)) {
                    $name = get_string('sectionname', 'format_'.$course->format) . ' ' . $section->section;
                } else {
                    $name = $section->name;
                }

                $href = new moodle_url('/course/view.php', [
                    'id'=> $this->course_id, 'section' => $section->section
                ]);

                $theme = new stdClass();
                $theme->id = $section->id;
                $theme->section = $section->section;
                $theme->name = $name;
                $theme->href = $href->out(false);
                $theme->active = $param === $section->section;
                $themes[] = $theme;
            }
        }
        return $themes;
    }

    /**
     * Get Pending Tasks.
     *
     * @return array
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_pending_tasks () {
        global $CFG, $PAGE;
        require_once($CFG->dirroot.'/calendar/lib.php');
        $course = get_course($this->course_id);
        $calendar = \calendar_information::create(time(), $this->course_id, $course->category);
        list($data, $template) = calendar_get_view($calendar, 'upcoming_mini');
        $tasks = [];
        foreach ($data->events as $event) {
            $task = new stdClass();
            $module = new module($event->instance);
            $task->modname = $module->get_cm_info()->modname;
            $task->name = $module->get_cm_info()->name;
            $task->deadline = userdate(
                $event->timeusermidnight,
                get_string('strftimedatefullshort', 'core_langconfig'));
            $event->timeusermidnight;
            $task->view_href = $event->viewurl;
            $tasks[] = $task;
        }
        return $tasks;
    }

    /**
     * Get Section 0.
     *
     * @return section_info
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_section_zero(): section_info {
        $course = get_course($this->course_id);
        /** @var course_modinfo $modinfo*/
        $modinfo = get_fast_modinfo($course->id);
        $sections = $modinfo->get_section_info_all();
        $section0 = null;
        foreach ($sections as $section) {
            if ($section->section === 0) {
                $section0 = $section;
            }
        }
        if (is_null($section0)) {
            throw new moodle_exception(get_string('section_zero_error', 'theme_cbe'));
        } else {
            return $section0;
        }
    }
}