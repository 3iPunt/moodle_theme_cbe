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
 * Class user
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use coding_exception;
use core_user;
use dml_exception;
use stdClass;
use user_picture;

defined('MOODLE_INTERNAL') || die;

/**
 * Class user
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user  {

    /** @var int User ID */
    protected $user_id;

    /** @var stdClass User */
    protected $user;

    /**
     * constructor.
     *
     * @param int $user_id
     * @param stdClass|null $user
     * @throws dml_exception
     */
    public function __construct(int $user_id, stdClass $user = null) {
        $this->user_id = $user_id;
        if (isset($user)) {
            $this->user = $user;
        } else {
            $this->user = core_user::get_user($user_id);
        }
    }

    /**
     * Export module for render.
     *
     * @return stdClass
     * @throws coding_exception
     */
    public function export(): stdClass {
        $user = new stdClass();
        $user->id = $this->user_id;
        $user->fullname = $this->get_fullname();
        $user->picture = $this->get_picture();
        $user->is_connected = $this->is_connected();
        return $user;
    }

    /**
     * Get Fullname
     *
     * @return string
     */
    public function get_fullname(): string {
        return fullname($this->user);
    }

    /**
     * Get Picture
     *
     * @return string
     * @throws coding_exception
     */
    public function get_picture(): string {
        global $PAGE;
        $userpicture = new user_picture($this->user);
        $userpicture->size = 1;
        return $userpicture->get_url($PAGE)->out(false);
    }

    /**
     * Is Connected?
     *
     * @return string
     */
    public function is_connected(): string {
        $now = time();
        $lastaccess = $this->user->lastaccess;
        return $now - (int)$lastaccess < 900;
    }

}
