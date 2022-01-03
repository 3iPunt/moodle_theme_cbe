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

use coding_exception;
use core_user;
use dml_exception;
use theme_cbe\course;
use theme_cbe\course_user;
use theme_cbe\models\board;
use theme_cbe\module;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\publication;
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

    /** @var int Course Module ID Anchor */
    protected $anchor = null;

    /** @var board Board Model */
    protected $board = null;

    /**
     * board_page constructor.
     * @param int $course_id
     * @param int|null $pub
     * @throws dml_exception
     */
    public function __construct(int $course_id, int $pub = null) {
        $this->course_id = $course_id;
        $this->pub = $pub;
        $this->board = new board($this->course_id);
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

        $avatar_api = get_config('theme_cbe', 'avatar_api');
        if ($avatar_api) {
            $pictureurl = get_config('theme_cbe', 'avatar_api_url');
        } else {
            $userpicture = new user_picture($user);
            $userpicture->size = 1;
            $pictureurl = $userpicture->get_url($PAGE)->out(false);
        }

        $user->fullname = fullname($user);
        $user->picture = $pictureurl;
        $user->is_connected = true;

        $course_user = new course_user($this->course_id, $user->id);
        $course_cbe = new course($this->course_id);

        $mods = $course_user->get_modules(true);

        if (isset($this->pub)) {
            foreach ($mods as $mod) {
                if ($mod->id === $this->pub) {
                    $mod->is_expand = true;
                }
            }
        }

        // Logical in Board
        $mods = $this->set_logical($mods);

        $data = new stdClass();
        $data->courseid = $this->course_id;
        $data->user = $user;
        $data->is_teacher = course_user::is_teacher($this->course_id);
        $data->modules = $mods;
        $themes = $course_cbe->get_themes();
        $unselect = ['section' => 'not', 'name' => get_string('create_module_theme', 'theme_cbe')];
        //array_unshift($themes, $unselect);
        $data->themes = $themes;
        $data->create = module::get_list_modules($this->course_id);
        $data->students = $course_cbe->get_users_by_role('student');
        $data->groups = $course_cbe->get_groups();
        return $data;
    }

    /**
     * Set Logical in Board.
     *
     * @param $mods
     * @return mixed
     * @throws coding_exception
     */
    protected function set_logical($mods): array {
        $newmods = [];
        $anchor = null;
        foreach ($mods as $mod) {
            if ($this->is_hidden($mod)) {
                $mod->board_is_hidden = true;
                if (course_user::is_teacher($this->course_id)) {
                    $newmods[] = $mod;
                }
            } else {
                if ($this->is_anchor($mod)) {
                    $mod->board_is_anchor = true;
                    $anchor = $mod;
                } else {
                    $newmods[] = $mod;
                }
            }
        }
        if (!is_null($anchor)) {
            array_unshift($newmods, $anchor);
        }
        return $newmods;
    }

    /**
     * Is Hidden?
     *
     * @param $mod
     * @return false
     */
    protected function is_hidden($mod): bool {
        if ($mod->modname === publication::MODULE_PUBLICATION) {
            return false;
        } else {
            if (!empty($this->board->get_ordermodules())) {
                return !in_array($mod->id, $this->board->get_ordermodules());
            } else {
                return false;
            }
        }
    }

    /**
     * Is Anchor?
     *
     * @param $mod
     * @return false
     */
    protected function is_anchor($mod): bool {
        return ($mod->id === $this->board->get_anchor());
    }

}
