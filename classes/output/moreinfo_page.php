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
 * Class moreinfo_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use coding_exception;
use dml_exception;
use moodle_url;
use theme_cbe\course;
use theme_cbe\course_user;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\module;
use theme_cbe\publication;

defined('MOODLE_INTERNAL') || die;

/**
 * Class moreinfo_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moreinfo_page implements renderable, templatable {

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
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $USER;
        $course = new course($this->course_id);
        $course_user = new course_user($this->course_id, $USER->id);
        $section = $course->get_section_zero();

        $data = new stdClass();
        $data->summary = $section->summary;
        $mods = $course_user->get_modules($section);
        // Filter NOT Publication.
        $mods = array_filter($mods, function(stdClass $mod) {
            return $mod->modname != publication::MODULE_PUBLICATION;
        }, ARRAY_FILTER_USE_BOTH);
        $mods = array_values($mods);
        $data->mods = $mods;
        $data->href_edit_section_zero = new moodle_url('/course/editsection.php', ['id'=> $section->id]);
        $data->create = module::get_list_modules($this->course_id, 0);
        $data->is_teacher = course_user::is_teacher($this->course_id);
        return $data;
    }
}
