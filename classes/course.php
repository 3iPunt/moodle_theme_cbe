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

    /**
     * constructor.
     *
     * @param int $course_id
     */
    public function __construct(int $course_id) {
        $this->course_id = $course_id;
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
            $row->is_connected = true;
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
                    $name = 'Topic ' . $section->section;
                } else {
                    $name = $section->name;
                }

                $href = new moodle_url('/course/view.php', [
                    'id'=> $this->course_id, 'section' => $section->section
                ]);

                $theme = new stdClass();
                $theme->name = $name;
                $theme->href = $href->out(false);
                $theme->active = $param === $section->section;
                $themes[] = $theme;
            }
        }
        return $themes;
    }
}