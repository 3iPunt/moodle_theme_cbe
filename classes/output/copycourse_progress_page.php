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
 * Class copycourse_progress_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use coding_exception;
use core_backup_renderer;
use dml_exception;
use moodleform;
use theme_cbe\course_user;
use moodle_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die;

/**
 * Class copycourse_progress_page
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copycourse_progress_page implements renderable, templatable {

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
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $OUTPUT, $PAGE, $USER;

        /** @var core_backup_renderer $renderer */
        $renderer = $PAGE->get_renderer('core', 'backup');

        $data = new stdClass();
        $data->heading = $OUTPUT->heading_with_help(
            get_string('copyprogressheading', 'backup'), 'copyprogressheading', 'backup');
        $data->viewer = $renderer->copy_progress_viewer($USER->id, $this->course_id);

        //echo $OUTPUT->container_start();

        //echo $OUTPUT->container_end();


        return $data;
    }
}
