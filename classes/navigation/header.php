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
 * Class header
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class header
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header extends render_cbe {

    /**
     * Set template.
     */
    protected function set_template() {
        if (isset($this->navigation)) {
            $this->template = $this->navigation->get_template_header();
        } else {
            $this->template = navigation::TEMPLATE_HEADER_DEFAULT;
        }
    }

    /**
     * Get data.
     *
     * @param array|stdClass $data
     */
    protected function set_data($data) {
        if (isset($this->navigation)) {
            $this->data = $this->navigation->get_data_header($data);
        } else {
            $data->nav_context = '';
            $data->nav_cbe = '';
            $data->courseimage = '';
            $data->contract = false;
            $data->teachers = [];
            $data->is_teacher = false;
            $data->coursename = '';
            $data->categoryname =  '';
            $this->data = $data;
        }
    }
}
