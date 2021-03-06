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
 * Class course_left_section_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use coding_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\navigation\course_module_navigation;
use theme_cbe\navigation\course_navigation;

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_left_section_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_left_section_component implements renderable, templatable {

    /** @var int Course ID */
    protected $course_id;

    /** @var int Course Module ID */
    protected $cm_id;

    /**
     * constructor.
     *
     * @param int $course_id
     * @param int|null $cm_id
     */
    public function __construct(int $course_id, int $cm_id = null) {
        $this->course_id = $course_id;
        $this->cm_id = $cm_id;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        if (empty($this->cm_id)) {
            $data->sections = course_navigation::left_section($this->course_id);
        } else {
            $data->sections = course_module_navigation::left_section($this->course_id);
        }
        return $data;
    }
}
