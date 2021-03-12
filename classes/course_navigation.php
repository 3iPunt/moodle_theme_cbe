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

defined('MOODLE_INTERNAL') || die;

/**
 * Class course_navigation
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_navigation  {

    const PAGE_BOARD = '/local/cbe/view_board.php';
    const PAGE_THEMES = '/course/view.php';
    const PAGE_TASKS = '/local/cbe/view_tasks.php';
    const PAGE_VCLASSES = '/local/cbe/view_virtualclasses.php';
    const PAGE_MOREINFO = '/local/cbe/view_moreinfo.php';

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

}