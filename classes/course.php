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
     */
    public function get_name(): string {
        return $this->course->fullname;
    }

    /**
     * Get Category.
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_category(): string {
        $category_id = $this->course->category;
        $category = core_course_category::get($category_id);
        return $category->get_formatted_name();
    }

    /**
     * Get Courseimage.
     *
     * @return string
     * @throws dml_exception
     */
    public function get_courseimage(): string {
        return course_summary_exporter::get_course_image($this->course);
    }

    /**
     * Get Users by role.
     *
     * @return array
     * @throws dml_exception
     * @throws coding_exception
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
            $userpicture = new user_picture($item);
            $userpicture->size = 1;
            $pictureurl = $userpicture->get_url($PAGE)->out(false);
            $row = new stdClass();
            $row->id = $item->id;
            $row->fullname = fullname($item);
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
     * @throws moodle_exception
     */
    public function get_themes(): array {
        $param = optional_param('section', null, PARAM_INT);
        /** @var course_modinfo $modinfo */
        $modinfo = get_fast_modinfo($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $themes = array();
        foreach ($sections as $section) {
            if ($section->section > 0) {
                if (is_null($section->name)) {
                    $name = get_string('sectionname', 'format_'.
                            $this->course->format) . ' ' . $section->section;
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
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_pending_tasks (): array {
        global $CFG, $PAGE;
        require_once($CFG->dirroot.'/calendar/lib.php');
        $calendar = \calendar_information::create(time(), $this->course_id, $this->course->category);
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
     * @throws moodle_exception
     */
    public function get_section_zero(): section_info {
        /** @var course_modinfo $modinfo*/
        $modinfo = get_fast_modinfo($this->course->id);
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
