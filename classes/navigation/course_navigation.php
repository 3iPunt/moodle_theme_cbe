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
 * Class course_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use coding_exception;
use html_writer;
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
use theme_cbe\user;

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_navigation extends navigation {

    const PAGE_BOARD = 'theme/cbe/view_board.php';
    const PAGE_THEMES = 'course/view.php';
    const PAGE_TASKS = 'theme/cbe/view_tasks.php';
    const PAGE_VCLASSES = 'theme/cbe/view_virtualclasses.php';
    const PAGE_MOREINFO = 'theme/cbe/view_moreinfo.php';
    const PAGE_RESOURCES = 'theme/cbe/view_resources.php';
    const PAGE_COPYCOURSE = 'theme/cbe/view_copycourse.php';
    const PAGE_COPYCOURSE_PROGRESS = 'theme/cbe/view_copycourse_progress.php';

    /** @var array Templates Header */
    protected $templates_header = [
        'board' => 'theme_cbe/header/board',
        'themes' => 'theme_cbe/header/themes',
        'tasks' => 'theme_cbe/header/custom',
        'vclasses' => 'theme_cbe/header/custom',
        'copycourse' => 'theme_cbe/header/custom',
        'resources' => 'theme_cbe/header/custom',
        'moreinfo' => 'theme_cbe/header/custom',
        'modedit' => 'theme_cbe/header/custom',
        'generic' => 'theme_cbe/header/custom',
        'default' => 'theme_cbe/header/header',
        'index' => 'theme_cbe/header/system',
        'calendar' => 'theme_cbe/header/custom',
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
        if ($this->get_page() === 'index' || $this->get_page() === 'calendar') {
            return 'theme_cbe/columns2/columns2_index';
        } else if ($this->get_page() === 'modedit') {
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
        $pagetype = $PAGE->pagetype;
        if ($pagetype === 'theme-cbe-view_board') {
            return 'board';
        } else if ($pagetype === 'course-view-topics') {
            if (strpos($path, 'user')) {
                return 'generic';
            } else {
                return 'themes';
            }
        } else if ($pagetype === 'theme-cbe-view_tasks') {
            return 'tasks';
        } else if ($pagetype === 'theme-cbe-view_resources') {
            return 'resources';
        } else if ($pagetype === 'theme-cbe-view_virtualclasses') {
            return 'vclasses';
        } else if ($pagetype === 'theme-cbe-view_moreinfo') {
            return 'moreinfo';
        } else if ($pagetype === 'theme-cbe-view_copycourse') {
            return 'copycourse';
        } else if ($pagetype === 'theme-cbe-view_copycourse_progress') {
            return 'copycourse';
        } else if ($pagetype === 'theme-cbe-view_editboard') {
            return 'editboard';
        } else if ($pagetype === 'site-index') {
            return 'index';
        } else if (strpos($path, 'modedit.php') ) {
            return 'modedit';
        } else if ($pagetype === 'calendar-view' ||
            $pagetype === 'calendar-export' ||
            $pagetype === 'calendar-managesubscriptions') {
            return 'calendar';
        } else {
            return 'generic';
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
        global $PAGE, $OUTPUT, $SITE;
        $courseid = $PAGE->context->instanceid;
        $coursecbe = new course($courseid);
        $data->nav_context = 'course';
        if ($this->get_page() === 'index') {
            $data->courseimage = $OUTPUT->get_generated_image_for_id(self::IMAGE_DEFAULT_SITE);
            $data->site = $SITE->fullname;
            $data->title = get_string('sitehome');
        } else {
            $data->courseimage = $coursecbe->get_courseimage();
        }
        $data->teachers = $coursecbe->get_users_by_role('editingteacher');
        $data->is_teacher = course_user::is_teacher($courseid);
        $data->can_create_courses = user::can_create_courses();
        $data->coursename = $coursecbe->get_name();
        $data->categoryname = $coursecbe->get_category();
        $data->edit_course= new moodle_url('/course/edit.php', ['id'=> $courseid]);
        $data->copy_course = new moodle_url('/' . self::PAGE_COPYCOURSE, ['id'=> $courseid]);
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
        $course_id = $PAGE->context->instanceid;

        $course_left_menu_component = new course_left_section_component($course_id);
        $course_left_menu = $output_theme_cbe->render($course_left_menu_component);
        $menu_apps_button_component = new menu_apps_button($this->header_api);
        $menu_apps_button = $output_theme_cbe->render($menu_apps_button_component);

        if ($this->get_page() !== 'index') {
            $nav_header_course_component = new course_header_navbar_component($course_id);
            $nav_header_course = $output_theme_cbe->render($nav_header_course_component);
        } else {
            $nav_header_course = null;
        }

        $cbe_page = course_navigation::get_page();
        if ($cbe_page === 'board' ||
            $cbe_page === 'themes' ||
            $cbe_page === 'moreinfo' ||
            $cbe_page === 'modedit'  ||
            $cbe_page === 'module') {
            $is_course_blocks = true;
        } else {
            $is_course_blocks = false;
        }

        if ($cbe_page === 'modedit') {
            $modname = optional_param('add', null, PARAM_RAW);
            if (!is_null($modname)) {
                $data['has_icon'] = true;
                if (in_array($modname, module::$resources)) {
                    $data['is_resource'] = true;
                    $activitylink = html_writer::start_tag('div', array('class' => 'cbe_icon_mod resource'));
                    $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
                    $classname = 'theme_cbe\output\module_' . $modname . '_icon_component';
                    $module_resource_icon_component = new $classname();
                    $resourcemod = $output_theme_cbe->render($module_resource_icon_component);
                    $activitylink .= $resourcemod;
                } else {
                    $data['is_resource'] = false;
                    $activitylink = html_writer::start_div('cbe_icon_mod');
                    $activitylink .= html_writer::empty_tag(
                        'span',
                        array(
                            'class' => 'iconlarge activityicon ' .$modname));
                }
                $activitylink .= html_writer::end_div();
                $data['html_icon'] = $activitylink;
            }
            if ($modname === 'assign' || $modname === 'resource') {
                $data['has_create_file_nextcloud'] = true;
            }
        }

        $data['in_course'] = true;
        $data['course_left_menu'] = $course_left_menu;
        $data['navbar_header_course'] =  $nav_header_course;
        $data['is_course_blocks'] = $is_course_blocks;
        $data['is_teacher'] = course_user::is_teacher($course_id);
        $data['menu_apps_button'] = $menu_apps_button;
        $data['nav_context'] = 'course';

        $data['has_dd_link'] = get_config('theme_cbe', 'has_dd_link');
        $data['ddlink_url'] = get_config('theme_cbe', 'ddlink_url');

        $data['nav_cbe'] = $cbe_page;

        return $data;
    }

    /**
     * Is Current Page.
     *
     * @param string $page
     * @return bool
     */
    static function is_current_page(string $page): bool {
        global $PAGE;
        $path = $PAGE->url->get_path();
        return strpos($path, $page);
    }

    /**
     * Left Section
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     */
    static function left_section(int $course_id): array {
        global $PAGE;
        $path = $PAGE->url->get_path();
        if (strpos($path, self::PAGE_BOARD)) {
            return self::left_section_board($course_id);
        } else if (strpos($path, self::PAGE_THEMES)) {
            return self::left_section_themes($course_id);
        } else if (strpos($path, self::PAGE_TASKS)) {
            return [];
        } else if (strpos($path, self::PAGE_VCLASSES)) {
            return [];
        } else if (strpos($path, self::PAGE_MOREINFO)) {
            return self::left_section_themes($course_id);
        } else if (strpos($path, 'course/modedit')) {
            return self::left_section_themes($course_id);
        } else if (strpos($path, 'grade')) {
            return [];
        } else {
            return [];
        }
    }

}
