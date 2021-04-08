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
 * Class user_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use coding_exception;
use core_user;
use dml_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use user_picture;

defined('MOODLE_INTERNAL') || die;

/**
 * Class user_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_component implements renderable, templatable {

    /** @var int User ID */
    protected $user;

    /** @var int Course ID */
    protected $course_id;

    /**
     * charge_page constructor.
     * @param int $user_id
     * @param int $course_id
     * @throws dml_exception
     */
    public function __construct(int $user_id, int $course_id) {
        $this->user = core_user::get_user($user_id);
        $this->course_id = $course_id;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $PAGE;
        $userpicture = new user_picture($this->user);
        $userpicture->size = 1;
        $pictureurl = $userpicture->get_url($PAGE)->out(false);
        $now = time();
        $lastaccess = $this->user->lastaccess;

        $data = new stdClass();
        $data->fullname = fullname($this->user);
        $data->isonline = $now - (int)$lastaccess < 900;
        $data->userid = $this->user->id;
        $data->pictureurl = $pictureurl;
        $data->courseid = $this->course_id;
        return $data;
    }
}
