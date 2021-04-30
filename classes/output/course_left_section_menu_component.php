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
 * Class course_left_section_menu_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use context_course;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\navigation\course_navigation;

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_left_section_menu_component
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_left_section_menu_component implements renderable, templatable {

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

        $coursecontext = context_course::instance($this->course_id);
        if (has_capability('moodle/course:update', $coursecontext)) {
            $settings = new moodle_url('/course/edit.php', ['id'=> $this->course_id]);
        } else {
            $settings = false;
        }

        $links = [
            'resource' => new moodle_url('/' . course_navigation::PAGE_RESOURCES, ['id'=> $this->course_id]),
            'vclasses' => $this->get_vclasses_href(),
            'grades' => new moodle_url('/grade/report/index.php', ['id'=> $this->course_id]),
            'participants' => new moodle_url('/user/index.php', ['id'=> $this->course_id]),
            'settings' => $settings
        ];
        $data = new stdClass();
        $data->title = null;
        $data->links = $links;
        return $data;
    }

    /**
     * Get VClasses HREF
     *
     * @return stdClass
     * @throws moodle_exception
     */
    protected function get_vclasses_href (): stdClass {
        $vc = new stdClass();
        $vc->href = '#';
        $vc->blank = false;
        $vc->name = get_string('course_menu_virtual', 'theme_cbe');
        $hasmain = false;
        if (get_config('theme_cbe', 'vclasses_direct')) {
            $modules = get_coursemodules_in_course('bigbluebuttonbn', $this->course_id);
            foreach ($modules as $mod) {
                if ($mod->idnumber === 'MAIN') {
                    $href = new moodle_url('/mod/bigbluebuttonbn/bbb_view.php',
                        ['action'=> 'join', 'id' => $mod->id, 'bn' => $mod->instance]);
                    $vc->href = $href->out(false);
                    $vc->blank = true;
                    $hasmain = true;
                    break;
                }
            }
        }
        if (!$hasmain) {
            $vc->href = new moodle_url('/' . course_navigation::PAGE_VCLASSES, ['id'=> $this->course_id]);
            $vc->blank = false;
            $vc->name = get_string('course_menu_virtuals', 'theme_cbe');
        }
        return $vc;
    }
}
