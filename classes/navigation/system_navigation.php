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
 * Class system_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use coding_exception;
use moodle_exception;
use pix_icon;
use stdClass;
use theme_cbe\api\header_api;
use theme_cbe\output\menu_apps_button;

defined('MOODLE_INTERNAL') || die;

/**
 * Class system_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_navigation extends navigation {

    /** @var array Templates Header */
    protected $templates_header = [
        'system' => 'theme_cbe/header/system'
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
        return 'theme_cbe/columns2/columns2_system';
    }

    /**
    * Get Navigation Page.
    *
    * @return string
    */
    protected function get_page(): string {
        return 'system';
    }

    /**
     * Get Data Header.
     *
     * @param stdClass $data
     * @return stdClass
     */
    public function get_data_header(stdClass $data): stdClass {
        global $SITE, $OUTPUT;
        $data->nav_context = 'system';
        $data->site = $SITE->fullname;
        $data->title = $this->get_clean_title();
        $data->courseimage = $OUTPUT->get_generated_image_for_id(self::IMAGE_DEFAULT_SITE);
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
        $menu_apps_button_component = new menu_apps_button($this->header_api);
        $menu_apps_button = $output_theme_cbe->render($menu_apps_button_component);

        $data['in_course'] = false;
        $data['course_left_menu'] = false;
        $data['navbar_header_course'] = '';
        $data['is_course_blocks'] = false;
        $data['is_teacher'] = false;
        $data['menu_apps_button'] = $menu_apps_button;
        $data['nav_context'] = 'user';
        $data['create_course'] = false;
        $data['can_create_courses'] = false;
        $data['nav_cbe'] = $this->get_page();
        $data['logo'] = $this->get_logo();

        return $data;
    }

}
