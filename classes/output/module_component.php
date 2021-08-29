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
 * Class module_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use cm_info;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\module;

defined('MOODLE_INTERNAL') || die;

/**
 * Class module_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_component implements renderable, templatable {

    /** @var int CM ID */
    protected $cmid;

    /** @var stdClass Course */
    protected $course;

    /** @var cm_info CM */
    protected $cm;

    /**
     * module_component constructor.
     *
     * @param int $cmid
     * @throws moodle_exception
     */
    public function __construct(int $cmid) {
        list($this->course, $this->cm) = get_course_and_cm_from_cmid($cmid);
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->modname = $this->cm->modname;
        $data->modfullname = $this->cm->modfullname;
        $data->name = $this->cm->name;
        $data->is_resource = in_array($this->cm->modname, module::$resources);
        $data->view_url = new moodle_url('/mod/' . $this->cm->modname. '/view.php', ['id'=> $this->cm->id]);;
        $data->sectionname = get_section_name($this->course->id, $this->cm->sectionnum);
        return $data;
    }
}
