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
use stdClass;

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

    /** @var string Order */
    protected $order;

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
            $this->order = $board->order;
            $this->anchor = $board->anchor;
            $this->userid = $board->userid;
            $this->timemodified = $board->timemodified;
        } else {
            $this->id = 0;
            $this->order = [];
            $this->anchor = 0;
        }
    }

    /**
     * Get Order
     *
     * @return string[]
     */
    public function get_order(): array {
        if (!empty($this->order)) {
            $order = explode(',', $this->order);
            return $order ? $order : [];
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
            $board->course = $this->course;
            $board->order = null;
            $board->anchor = $cmid;
            $board->userid = $USER->id;
            $board->timemodified = time();
            return $DB->insert_record(self::BOARD_TABLE, $board);
        } else {
            $board = new stdClass();
            $board->id = $this->id;
            $board->course = $this->course;
            $board->order = $this->order;
            $board->anchor = $cmid;
            $board->userid = $USER->id;
            $board->timemodified = time();
            return $DB->update_record(self::BOARD_TABLE, $board);
        }
    }

    /**
     * Update Order.
     *
     * @param int $cmid
     * @param bool $visible
     * @return bool|int
     * @throws dml_exception
     */
    public function update_order(int $cmid, bool $visible) {
        global $DB, $USER;
        if ($this->id === 0) {
            $board = new stdClass();
            $board->course = $this->course;
            $board->order = $visible ? $cmid : null;
            $board->anchor = null;
            $board->userid = $USER->id;
            $board->timemodified = time();
            return $DB->insert_record(self::BOARD_TABLE, $board);
        } else {
            $board = new stdClass();
            $board->id = $this->id;
            $board->course = $this->course;
            $board->order = $this->update_order_column($cmid, $visible);
            $board->anchor = $this->get_anchor();
            $board->userid = $USER->id;
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
     */
    protected function update_order_column(int $cmid, bool $visible) {
        $ordernew = $this->get_order();
        $orderold = $this->get_order();
        return implode(",", $ordernew);
    }

}