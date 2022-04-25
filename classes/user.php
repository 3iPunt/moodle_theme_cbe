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
use context_system;
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
     * @throws dml_exception
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
     * @throws dml_exception
     */
    public function get_picture(): string {
        global $PAGE, $USER;
        $avatar_api = get_config('theme_cbe', 'avatar_api');
        if ($avatar_api) {
            if ($USER->id === $this->user->id) {
                return get_config('theme_cbe', 'avatar_api_url');
            } else {
                $src = get_config('theme_cbe', 'avatar_other_users');
                $userdata = core_user::get_user($this->user->id);
                return $src . $userdata->username;
            }
        } else {
            $userpicture = new user_picture($this->user);
            $userpicture->size = 1;
            return $userpicture->get_url($PAGE)->out(false);
        }
    }

    /**
     * Is Connected?
     *
     * @return string
     */
    public function is_connected(): string {
        $now = time();
        $lastaccess = isset($this->user->lastaccess) ? $this->user->lastaccess: 0;
        return $now - (int)$lastaccess < 900;
    }

    /**
     * Can course create?
     *
     * @return bool
     * @throws coding_exception|dml_exception
     */
    static public function can_create_courses(): bool {
        $context = context_system::instance();
        return has_capability('moodle/course:create', $context);
    }

    /**
     * Can import Google Classroom?
     *
     * @return bool
     * @throws coding_exception|dml_exception
     */
    static public function can_import_gc(): bool {
        global $CFG;
        if (file_exists($CFG->dirroot.'/local/tresipuntimportgc/version.php')) {
            $importgc_enable = get_config('theme_cbe', 'importgc');
            if ($importgc_enable) {
                return has_capability('local/tresipuntimportgc:import',  context_system::instance());
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
