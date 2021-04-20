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

namespace theme_cbe;

use coding_exception;
use theme_cbe\output\course_left_section_menu_component;
use theme_cbe\output\course_left_section_pending_tasks_component;
use theme_cbe\output\course_left_section_themes_navigation_component;

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

    /**
    * Get Navigation Page.
    *
    * @return string
    */
    static function get_navigation_page(): string {
        global $PAGE;
        $path = $PAGE->url->get_path();
        if (strpos($path, self::PAGE_BOARD)) {
            return 'board';
        } else if (strpos($path, self::PAGE_THEMES)) {
            return 'themes';
        } else if (strpos($path, self::PAGE_TASKS)) {
            return 'tasks';
        } else if (strpos($path, self::PAGE_VCLASSES)) {
            return 'vclasses';
        } else if (strpos($path, self::PAGE_MOREINFO)) {
            return 'moreinfo';
        } else if (strpos($path, 'grade') ||
                   strpos($path, 'user') ||
                   strpos($path, 'calendar') ||
                   strpos($path, 'contentbank') ||
                   strpos($path, 'course/modedit') ||
                   strpos($path, 'course/edit'
                   )) {
            return 'generic';
        } else {
            return '';
        }
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
        } else if (strpos($path, 'grade')) {
            return [];
        } else {
            return [];
        }
    }

}