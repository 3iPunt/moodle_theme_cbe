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
 * Class user_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use coding_exception;
use moodle_exception;
use moodle_url;
use stdClass;
use theme_cbe\output\menu_apps_button;

defined('MOODLE_INTERNAL') || die;

/**
 * Class user_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_navigation extends navigation {

    /** @var array Templates Header */
    protected $templates_header = [
        'user' => 'theme_cbe/header/user'
    ];

    /**
     * constructor.
     */
    public function __construct() {
    }

    /**
     * Get Template Layout.
     *
     * @return string
     */
    public function get_template_layout(): string {
        if ($this->is_contract()) {
            return 'theme_cbe/columns2/columns2_user';
        } else {
            return 'theme_cbe/columns2/columns2';
        }

    }

    /**
    * Get Navigation Page.
    *
    * @return string
    */
    protected function get_page(): string {
        return 'user';
    }

    /**
     * Get Data Header.
     *
     * @param stdClass $data
     * @return stdClass
     */
    public function get_data_header(stdClass $data): stdClass {
        $data->nav_context = 'user';
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
        $menu_apps_button_component = new menu_apps_button();
        $menu_apps_button = $output_theme_cbe->render($menu_apps_button_component);

        $data['in_course'] = false;
        $data['course_left_menu'] = false;
        $data['navbar_header_course'] = '';
        $data['is_course_blocks'] = false;
        $data['is_teacher'] = false;
        $data['menu_apps_button'] = $menu_apps_button;
        $data['nav_context'] = 'user';
        $data['nav_cbe'] = $this->get_page();

        return $data;
    }


    /**
    * Is Contract.
    *
    * @return bool
    */
    protected function is_contract(): bool {
        global $PAGE;
        $path = $PAGE->url->get_path();
        if (strpos($path, 'my/')) {
            return false;
        } else {
            return true;
        }
    }
}
