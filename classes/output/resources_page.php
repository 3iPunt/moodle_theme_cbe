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
 * Class resources_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use theme_cbe\tables\resources_table;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die;

/**
 * Class resources_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resources_page implements renderable, templatable {

    /** @var int Course ID */
    protected $course_id;

    /**
     * charge_page constructor.
     * @param int $course_id
     */
    public function __construct(int $course_id) {
        $this->course_id = $course_id;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return false|stdClass|string
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $USER;
        $data = new stdClass();
        $table = new resources_table($this->course_id, $USER->id);
        ob_start();
        $table->out(100, true);
        $output = ob_get_contents();
        ob_end_clean();
        $data->table = $output;
        return $data;
    }
}
