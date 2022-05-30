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
use theme_cbe\output\core_renderer;

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
     */
    public function get_name(): string {
        return $this->course->fullname;
    }

    /**
     * Get Category.
     *
     * @return string
     */
    public function get_category(): string {
        $category_id = $this->course->category;
        try {
            $category = core_course_category::get($category_id);
            return $category->get_formatted_name();
        } catch (moodle_exception $e) {
            return '';
        }
    }

    /**
     * Get Courseimage.
     *
     * @return string
     */
    public function get_courseimage(): string {
        global $OUTPUT;
        $courseimage = course_summary_exporter::get_course_image($this->course);
        if (!$courseimage) {
            $courseimage = $OUTPUT->get_generated_image_for_id($this->course->id);
        }
        return $courseimage;
    }

    /**
     * Get Users by role.
     *
     * @param string $role
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_users_by_role(string $role): array {
        global $PAGE, $DB;
        $role = $DB->get_record('role', array('shortname' => $role));
        $enrolmanager = new course_enrolment_manager($PAGE, $this->course, $instancefilter = null, $role->id,
            $searchfilter = '', $groupfilter = 0, $statusfilter = -1);
        $users = $enrolmanager->get_users(
            'u.lastname', 'ASC', 0, 0
        );
        $data = [];
        foreach ($users as $item) {
            $user_cbe = new user($item->id, $item);
            $data[] = $user_cbe->export();
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
            if (strpos($group->idnumber, 'GI_') === false) {
                $row = new stdClass();
                $row->id = $group->id;
                $row->name = $group->name;
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get Themes.
     *
     * @throws moodle_exception
     */
    public function get_themes(): array {
        global $PAGE;
        $section_current = null;
        switch ($PAGE->context->contextlevel) {
            case CONTEXT_COURSE:
                $section_current = optional_param('section', null, PARAM_INT);
                break;
            case CONTEXT_MODULE:
                $cmid = $PAGE->context->instanceid;
                list($course, $cm) = get_course_and_cm_from_cmid($cmid);
                $section_current = intval($cm->sectionnum);
                break;
        }
        /** @var course_modinfo $modinfo */
        $modinfo = get_fast_modinfo($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $themes = array();
        foreach ($sections as $section) {
            if ($section->section > 0) {
                if ($section->uservisible) {
                    $theme_cbe = new theme($section, $this->course);
                    $theme = $theme_cbe->export();
                    $theme->active = $section_current === $section->section;
                    $themes[] = $theme;
                }
            }
        }
        return $themes;
    }

    /**
     * Get Pending Tasks.
     *
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_pending_tasks (): array {
        global $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');
        $calendar = \calendar_information::create(time(), $this->course_id, $this->course->category);
        list($data, $template) = calendar_get_view($calendar, 'upcoming_mini');
        $tasks = [];
        foreach ($data->events as $event) {
            $task = new stdClass();
            if (isset($event->instance)) {
                $module = new module($event->instance);
                $task->modname = $module->get_cm_info()->modname;
                $task->name = $module->get_cm_info()->name;
                $task->deadline = userdate(
                    $event->timeusermidnight,
                    get_string('strftimedatefullshort', 'core_langconfig'));
                $event->timeusermidnight;
                $task->view_href = $module->get_view_href();
                $tasks[] = $task;
            }
        }
        return $tasks;
    }

    /**
     * Get Section 0.
     *
     * @return section_info|null
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_section_zero(): ?section_info {
        /** @var course_modinfo $modinfo*/
        $modinfo = get_fast_modinfo($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $section0 = null;
        foreach ($sections as $section) {
            if ($section->section === 0) {
                $section0 = $section;
            }
        }
        if (is_null($section0) && $this->course->id !== '1') {
            throw new moodle_exception(get_string('section_zero_error', 'theme_cbe'));
        } else {
            return $section0;
        }
    }

    /**
     * Get Title Section 0.
     *
     * @param int $course_id
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_title_section0(int $course_id): string {
        $course = new course($course_id);
        $section = $course->get_section_zero();
        $title = isset($section->name) ? $section->name : '';
        if (empty($title)) {
            $title = get_string('course_menu_moreinfo', 'theme_cbe');
        }
        return $title;
    }

    /**
     * Can delete course?
     *
     * @return bool
     * @throws coding_exception
     */
    public function can_delete_course(): bool {
        global $USER;
        return course_user::is_teacher($this->course_id, $USER->id);
    }
}
