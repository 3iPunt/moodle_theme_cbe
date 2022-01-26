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
 * Class board
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\models;

use coding_exception;
use dml_exception;
use moodle_exception;
use stdClass;
use theme_cbe\course;
use theme_cbe\course_user;

defined('MOODLE_INTERNAL') || die;

/**
 * Class board
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class board  {

    const BOARD_TABLE = 'theme_cbe_board';

    /** @var int ID */
    protected $id;

    /** @var int Course ID */
    protected $course;

    /** @var string Order Modules */
    protected $ordermodules;

    /** @var int Anchor Course Module ID */
    protected $anchor;

    /** @var int User ID last modification*/
    protected $userid;

    /** @var int Time Modified */
    protected $timemodified;

    /**
     * constructor.
     * @param int $course
     * @throws dml_exception
     */
    public function __construct(int $course) {
        $this->course = $course;
        $this->set_board();
    }

    /**
     * Set Board.
     *
     * @throws dml_exception
     */
    public function set_board(){
        global $DB;
        $board = $DB->get_record(self::BOARD_TABLE, ['course' => $this->course ]);
        if (!empty($board)) {
            $this->id = $board->id;
            $this->ordermodules = !empty($board->ordermodules) ? $board->ordermodules : null;
            $this->anchor = $board->anchor;
            $this->userid = $board->userid;
            $this->timemodified = $board->timemodified;
        } else {
            $this->id = 0;
            $this->ordermodules = [];
            $this->anchor = 0;
        }
    }

    /**
     * Get Order modules
     *
     * @return null
     */
    public function get_ordermodules(): ?array {
        if (!empty($this->ordermodules)) {
            $ordermodules = explode(',', $this->ordermodules);
            return $ordermodules ? $ordermodules : [];
        } else if (is_null($this->ordermodules)) {
            return null;
        } else {
            return [];
        }
    }

    /**
     * Get Anchor.
     *
     * @return int
     */
    public function get_anchor(): int {
        if (is_null($this->anchor)) {
            return 0;
        }
        return $this->anchor;
    }

    /**
     * Set Mod Anchor.
     *
     * @param int $cmid
     * @return bool|int
     * @throws dml_exception
     */
    public function set_mod_anchor(int $cmid) {
        global $DB, $USER;
        if ($this->id === 0) {
            $board = new stdClass();
            $board->course = (int)$this->course;
            $board->ordermodules = null;
            $board->anchor = (int)$cmid;
            $board->userid = (int)$USER->id;
            $board->timemodified = time();
            return $DB->insert_record(self::BOARD_TABLE, $board);
        } else {
            $board = new stdClass();
            $board->id = (int)$this->id;
            $board->course = (int)$this->course;
            $board->ordermodules = $this->ordermodules;
            $board->anchor = (int)$cmid;
            $board->userid = (int)$USER->id;
            $board->timemodified = time();
            return $DB->update_record(self::BOARD_TABLE, $board);
        }
    }

    /**
     * Remove Mod Anchor.
     *
     * @param int $cmid
     * @return bool|int
     * @throws dml_exception
     */
    public function remove_mod_anchor(int $cmid) {
        global $DB, $USER;
        if ($this->get_anchor() === $cmid && $this->anchor > 0) {
            $board = new stdClass();
            $board->id = (int)$this->id;
            $board->course = (int)$this->course;
            $board->ordermodules = $this->ordermodules;
            $board->anchor = null;
            $board->userid = (int)$USER->id;
            $board->timemodified = time();
            return $DB->update_record(self::BOARD_TABLE, $board);
        } else {
            return false;
        }
    }

    /**
     * Update Order.
     *
     * @param int $cmid
     * @param bool $visible
     * @return bool|int
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function update_ordermodules(int $cmid, bool $visible) {
        global $DB, $USER;
        if ($this->id === 0) {
            $board = new stdClass();
            $board->course = (int)$this->course;
            $board->ordermodules = $this->update_ordermodules_column($cmid, $visible);
            $board->anchor = null;
            $board->userid = (int)$USER->id;
            $board->timemodified = time();
            return $DB->insert_record(self::BOARD_TABLE, $board);
        } else {
            $board = new stdClass();
            $board->id = (int)$this->id;
            $board->course = (int)$this->course;
            $ordermodules = $this->update_ordermodules_column($cmid, $visible);
            $board->ordermodules = $this->update_ordermodules_column($cmid, $visible);
            $board->anchor = (int)$this->get_anchor();
            $board->userid = (int)$USER->id;
            $board->timemodified = time();
            return $DB->update_record(self::BOARD_TABLE, $board);
        }
    }

    /**
     * Update Order Column.
     *
     * @param int $cmid
     * @param bool $visible
     * @return string
     * @throws moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function update_ordermodules_column(int $cmid, bool $visible): string {

        $old = $this->get_ordermodules();

        if (count($old) > 0) {
            // Update.
            if ($visible) {
                $new = $old;
                if (!in_array((string)$cmid,$old)) {
                    array_push($new, $cmid);
                }
            } else {
                $new = [];
                foreach ($old as $item) {
                    if ((string)$item !== (string)$cmid) {
                        $new[] = $item;
                    }
                }
            }
        } else if (is_null($old)) {
            $new = [$cmid];
        } else {
            // Create.
            $new = $this->get_allidmods($cmid, $visible);
        }
        return implode(",", $new);
    }

    /**
     * Get ID mods.
     *
     * @param $cmid
     * @param bool $visible
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function get_allidmods($cmid, bool $visible): array {
        global $USER;
        $course_user = new course_user($this->course, $USER->id);
        $mods = $course_user->get_modules(true);
        $idmods = [];
        foreach ($mods as $mod) {
            if ($visible) {
                $idmods[] = $mod->id;
            } else {
                if ((string)$mod->id !== (string)$cmid) {
                    $idmods[] = $mod->id;
                }
            }
        }
        return $idmods;
    }

}