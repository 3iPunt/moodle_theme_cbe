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
 * Class course_module_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use cm_info;
use coding_exception;
use moodle_exception;
use moodle_url;
use pix_icon;
use stdClass;
use theme_cbe\api\header_api;
use theme_cbe\course;
use theme_cbe\course_user;
use theme_cbe\module;
use theme_cbe\output\course_header_navbar_component;
use theme_cbe\output\course_left_section_component;
use theme_cbe\output\menu_apps_button;

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_module_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_navigation extends navigation {

    /** @var array Templates Header */
    protected $templates_header = [
        'module' => 'theme_cbe/header/custom',
        'modedit' => 'theme_cbe/header/custom'
    ];

    /**
     * constructor.
     * @param header_api|null $header_api $header_api
     */
    public function __construct(header_api $header_api = null) {
        parent::__construct($header_api);
    }

    /**
     * Get Template Layout.
     *
     * @return string
     */
    public function get_template_layout(): string {
        if ($this->get_page() === 'modedit') {
            return 'theme_cbe/columns2/columns2_modedit';
        } else {
            return 'theme_cbe/columns2/columns2_course';
        }
    }

    /**
    * Get Page.
    *
    * @return string
    */
    protected function get_page(): string {
        global $PAGE;
        $path = $PAGE->url->get_path();
        if (strpos($path, 'modedit.php') ) {
            return 'modedit';
        } else {
            return 'module';
        }
    }

    /**
     * Get Data Header.
     *
     * @param stdClass $data
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_data_header(stdClass $data): stdClass {
        global $PAGE;
        $cmid = $PAGE->context->instanceid;
        list($course, $cm) = get_course_and_cm_from_cmid($cmid);
        $courseid = $course->id;
        $coursecbe = new course($courseid);
        $data->nav_context = 'course';
        $data->is_teacher = course_user::is_teacher($courseid);
        $data->coursename = $coursecbe->get_name();
        $data->categoryname = $coursecbe->get_category();
        $data->courseimage = $coursecbe->get_courseimage();
        $data->edit_course= new moodle_url('/course/edit.php', ['id'=> $courseid]);
        return $data;
    }

    /**
     * Get Data Layout.
     *
     * @param array $data
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_data_layout(array $data): array {
        global $PAGE;
        $output_theme_cbe = $PAGE->get_renderer('theme_cbe');

        $cmid = $PAGE->context->instanceid;
        list($course, $cm) = get_course_and_cm_from_cmid($cmid);
        $course_id = $course->id;

        $nav_header_course_component = new course_header_navbar_component($course_id);
        $nav_header_course = $output_theme_cbe->render($nav_header_course_component);
        $course_left_menu_component = new course_left_section_component($course_id, $cmid);
        $course_left_menu = $output_theme_cbe->render($course_left_menu_component);
        $menu_apps_button_component = new menu_apps_button($this->header_api);
        $menu_apps_button = $output_theme_cbe->render($menu_apps_button_component);

        $coursemodule = new module($cmid);

        $data['in_course'] = true;
        $data['course_left_menu'] = $course_left_menu;
        $data['navbar_header_course'] =  $nav_header_course;
        $data['is_course_blocks'] = true;
        $data['is_teacher'] = course_user::is_teacher($course_id);
        $data['menu_apps_button'] = $menu_apps_button;
        $data['nav_context'] = 'course';
        $data['is_module'] = true;
        $data['has_create_file_nextcloud'] = self::has_create_file_nextcloud($coursemodule);
        $data['nextcloud_desc'] = self::get_create_file_nextcloud_desc($coursemodule->get_modname());
        $data['module_title'] = $coursemodule->get_name();
        $data['html_icon'] = $coursemodule->get_html_icon();
        $data['is_resource'] = $coursemodule->is_resource();
        $data['nav_cbe'] = course_module_navigation::get_page();

        $data['has_dd_link'] = get_config('theme_cbe', 'has_dd_link');
        $data['ddlink_url'] = get_config('theme_cbe', 'ddlink_url');

        return $data;
    }

    /**
     * Has Create File Nextcloud?
     *
     * @param module $coursemodule
     * @return bool
     */
    static public function has_create_file_nextcloud(module $coursemodule): bool {
        return $coursemodule->get_modname() === 'assign' ||
            $coursemodule->get_modname() === 'resource' ||
            $coursemodule->get_modname() === 'tipnextcloud';
    }

    /**
     *  Create File Nextcloud Description
     *
     * @param module $coursemodule
     * @return bool
     * @throws coding_exception
     */


    /**
     * Get Create File Nextcloud Description
     *
     * @param string $coursemodulename
     * @return \lang_string|string
     * @throws coding_exception
     */
    static public function get_create_file_nextcloud_desc(string $coursemodulename) {
        if ($coursemodulename === 'tipnextcloud') {
            return get_string('notice_filecreate_tip', 'theme_cbe');
        } else {
            return get_string('notice_filecreate', 'theme_cbe');
        }
    }

    /**
     * Left Section
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     */
    static public function left_section(int $course_id): array {
        return self::left_section_themes($course_id);
    }
}
