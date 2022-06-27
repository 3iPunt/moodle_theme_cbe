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
 * Class layout
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use coding_exception;
use pix_icon;
use stdClass;
use theme_cbe\output\menu_apps_button;

defined('MOODLE_INTERNAL') || die;

/**
 * Class layout
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class layout extends render_cbe {

    /**
     * Set template.
     */
    protected function set_template() {
        if (isset($this->navigation)) {
            $this->template = $this->navigation->get_template_layout();
        } else {
            $this->template = navigation::TEMPLATE_LAYOUT_DEFAULT;
        }
    }

    /**
     * Get data.
     *
     * @param array|stdClass $data
     * @throws coding_exception
     */
    protected function set_data($data) {
        global $PAGE;
        if (isset($this->navigation)) {
            $this->data = $this->navigation->get_data_layout($data);
        } else {
            // TODO. Revisar esto, hay que quitarlo, meterlo en navigation.
            $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
            $menu_apps_button_component = new menu_apps_button($this->header_api);
            $menu_apps_button = $output_theme_cbe->render($menu_apps_button_component);
            $data['in_course'] = false;
            $data['course_left_menu'] = false;
            $data['navbar_header_course'] =  '';
            $data['is_course_blocks'] = false;
            $data['is_teacher'] = false;
            $data['menu_apps_button'] = $menu_apps_button;
            $data['nav_context'] =  '';
            $data['nav_cbe'] =  '';
            $data['contract'] = false;
            $this->data = $data;
        }
    }

    /**
     * Is DarkHeader?
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_darkheader(): bool {
        return get_config('theme_cbe', 'darkheader') ? true : false;
    }
}
