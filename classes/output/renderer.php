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
 * Class renderer
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_cbe\output;

defined('MOODLE_INTERNAL') || die;

use moodle_exception;
use plugin_renderer_base;

/**
 * Class renderer
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param course_header_navbar_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_course_header_navbar_component(course_header_navbar_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('theme_cbe/course_header_navbar_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param course_left_section_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_course_left_section_component(course_left_section_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('theme_cbe/course_left_section_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param participants_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_participants_page(participants_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('theme_cbe/participants_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param tasks_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_tasks_page(tasks_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('theme_cbe/tasks_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param resources_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_resources_page(resources_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('theme_cbe/resources_page', $data);
    }

    /**
     * User Component.
     *
     * @param user_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_user_component(user_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('theme_cbe/user_component', $data);
    }

    /**
     * Module Component.
     *
     * @param user_component $component
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_module_component(module_component $component): string {
        $data = $component->export_for_template($this);
        return parent::render_from_template('theme_cbe/module_component', $data);
    }

    /**
     * Defer to template.
     *
     * @param board_page $page
     *
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_board_page(board_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('theme_cbe/board_page', $data);
    }

}
