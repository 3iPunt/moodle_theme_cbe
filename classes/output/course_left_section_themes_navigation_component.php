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
 * Class course_left_section_themes_navigation_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\course;
use theme_cbe\course_navigation;
use theme_cbe\course_user;

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_left_section_themes_navigation_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_left_section_themes_navigation_component implements renderable, templatable {

    /** @var int Course ID */
    protected $course_id;

    /**
     * constructor.
     *
     * @param int $course_id
     */
    public function __construct(int $course_id) {
        $this->course_id = $course_id;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $course_cbe = new course($this->course_id);
        $data = new stdClass();
        $data->title = get_string('course_navigation', 'theme_cbe');
        $data->view_all = new moodle_url('/course/view.php', ['id'=> $this->course_id]);
        $data->themes = $course_cbe->get_themes();
        $data->is_teacher = course_user::is_teacher($this->course_id);
        $data->courseid = $this->course_id;
        return $data;
    }
}
