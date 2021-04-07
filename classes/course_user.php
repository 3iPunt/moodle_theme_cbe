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
use context_course;
use moodle_exception;
use moodle_url;
use stdClass;

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
     * @param int|null $userid
     * @return stdClass[]
     * @throws coding_exception|moodle_exception
     */
    static public function user_get_courses(): array {
        $data = [];
        foreach (enrol_get_my_courses() as $enrolcourse) {
            $course = new stdClass();
            $url = new moodle_url('/local/cbe/view_board.php', [ 'id'=> $enrolcourse->id ]);
            $course->fullname = $enrolcourse->fullname;
            $course->view_url = $url->out(false);
            $data[] = $course;
        }
        return $data;
    }

}