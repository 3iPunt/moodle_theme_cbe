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
 * Class board_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use core_user;
use theme_cbe\course;
use theme_cbe\course_user;
use theme_cbe\module;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use user_picture;

defined('MOODLE_INTERNAL') || die;

/**
 * Class board_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class board_page implements renderable, templatable {

    /** @var int Course ID */
    protected $course_id;

    /** @var int Pub ID in URL */
    protected $pub;

    /**
     * charge_page constructor.
     * @param int $course_id
     * @param int|null $pub
     */
    public function __construct(int $course_id, int $pub = null) {
        $this->course_id = $course_id;
        $this->pub = $pub;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return false|stdClass|string
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $PAGE;

        // Current User.
        $user = core_user::get_user($USER->id);

        $userpicture = new user_picture($user);
        $userpicture->size = 1;
        $pictureurl = $userpicture->get_url($PAGE)->out(false);

        $user->fullname = fullname($user);
        $user->picture = $pictureurl;
        $user->is_connected = true;

        $course_user = new course_user($this->course_id, $user->id);
        $course_cbe = new course($this->course_id);

        $mods = $course_user->get_modules();

        if (isset($this->pub)) {
            foreach ($mods as $mod) {
                if ($mod->id === $this->pub) {
                    $mod->is_expand = true;
                }
            }
        }

        $data = new stdClass();
        $data->courseid = $this->course_id;
        $data->user = $user;
        $data->is_teacher = course_user::is_teacher($this->course_id);
        $data->modules = $mods;
        $data->create = module::get_list_modules($this->course_id);
        $data->students = $course_cbe->get_users_by_role('student');
        $data->groups = $course_cbe->get_groups();
        return $data;
    }

}
