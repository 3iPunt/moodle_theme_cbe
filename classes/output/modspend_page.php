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
 * Block Tresipunt Modules Pending renderable
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\tables\modspend_delivery_table;
use theme_cbe\tables\modspend_grade_table;
use theme_cbe\tables\modspend_table;

/**
 * modspend_page renderable class.
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modspend_page implements renderable, templatable {

    /** @var string Rolename */
    protected $rolename;

    /**
     * tasks_page constructor.
     *
     * @param string $rolename
     * @throws moodle_exception
     */
    public function __construct(string $rolename) {
        if ($rolename === 'editingteacher' || $rolename === 'student') {
            $this->rolename = $rolename;
        } else {
            throw new moodle_exception(get_string('role_bad', 'block_tresipuntmodspend'));
        }
    }

    /**
     * Export for Template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $USER;
        $data = new stdClass();
        if ($this->rolename === 'editingteacher') {
            $table = new modspend_grade_table($USER->id, $this->rolename);
        } else {
            $table = new modspend_delivery_table($USER->id, $this->rolename);
        }
        ob_start();
        $table->out(100, true);
        $output = ob_get_contents();
        ob_end_clean();
        $data->table = $output;
        return $data;
    }
}

