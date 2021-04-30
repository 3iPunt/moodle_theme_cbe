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
 * Class course_user
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

global $CFG;

use coding_exception;
use comment_exception;
use context_course;
use dml_exception;
use moodle_exception;
use moodle_url;
use section_info;
use stdClass;
use theme_cbe\navigation\course_navigation;

require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/modinfolib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_user
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_user  {

    /** @var int Course ID */
    protected $course_id;

    /** @var int User ID */
    protected $user_id;

    /**
     * constructor.
     *
     * @param int $course_id
     * @param int $user_id
     */
    public function __construct(int $course_id, int $user_id) {
        $this->course_id = $course_id;
        $this->user_id = $user_id;
    }

    /**
     * Get Modules.
     *
     * @param section_info|null $section
     * @return array
     * @throws comment_exception
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_modules(section_info $section = null): array {
        $modinfo = get_fast_modinfo($this->course_id);
        $cms = $modinfo->get_cms();

        if (is_null($section)) {
            // Order by added DESC.
            usort($cms, function($a, $b) {
                return $b->added <=> $a->added;
            });
        }

        $modules = [];

        foreach ($cms as $cm) {
            if ($cm->is_visible_on_course_page()) {
                if (is_null($section) || $section->section == $cm->sectionnum) {
                    $module = new module($cm->id);
                    $modules[] = $module->export();
                }
            }
        }
        return $modules;
    }

    /**
     * Is Teacher?
     *
     * if userid is null, then $USER will be used.
     *
     * @param int $courseid
     * @param int|null $userid
     * @return bool
     * @throws coding_exception
     */
    static public function is_teacher(int $courseid, int $userid = null): bool {
        $coursecontext = context_course::instance($courseid);
        return has_capability('moodle/course:update', $coursecontext, $userid);
    }

    /**
     * User Get Courses;
     *
     * @return stdClass[]
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function user_get_courses(): array {
        $data = [];
        foreach (enrol_get_my_courses() as $enrolcourse) {
            $course = new stdClass();
            $url = new moodle_url('/' . course_navigation::PAGE_BOARD, [ 'id'=> $enrolcourse->id ]);
            $course->fullname = $enrolcourse->fullname;
            $course->view_url = $url->out(false);
            $data[] = $course;
        }
        return $data;
    }

}