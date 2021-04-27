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
 * Class navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use coding_exception;
use theme_cbe\output\course_left_section_menu_component;
use theme_cbe\output\course_left_section_pending_tasks_component;
use theme_cbe\output\course_left_section_themes_navigation_component;

defined('MOODLE_INTERNAL') || die;

/**
 * Class navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation  {

    /**
     * Left Section Board in Course.
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     */
    static function left_section_board(int $course_id): array {
        $sections = [];
        $sections[] = self::section_pending_tasks($course_id);
        $sections[] = self::section_menu_left($course_id);
        return $sections;
    }

    /**
     * Left Section Themes in Course.
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     */
    static function left_section_themes(int $course_id): array {
        $sections = [];
        $sections[] = self::section_themes_navigation($course_id);
        $sections[] = self::section_menu_left($course_id);
        return $sections;
    }

    /**
     * Section Themes Navigation.
     *
     * @param int $course_id
     * @return bool|string
     * @throws coding_exception
     */
    static function section_themes_navigation(int $course_id){
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $renderer = new course_left_section_themes_navigation_component($course_id);
        return $output->render($renderer);
    }

    /**
     * Section Pending Tasks.
     *
     * @param int $course_id
     * @return bool|string
     * @throws coding_exception
     */
    static function section_pending_tasks(int $course_id){
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $renderer = new course_left_section_pending_tasks_component($course_id);
        return $output->render($renderer);
    }

    /**
     * Section Menu Left.
     *
     * @param int $course_id
     * @return bool|string
     * @throws coding_exception
     */
    static function section_menu_left(int $course_id) {
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $renderer = new course_left_section_menu_component($course_id);
        return $output->render($renderer);
    }

    /**
     * Is Contract.
     *
     * @return string
     */
    static function is_contract(): string {
        return true;
    }

}
