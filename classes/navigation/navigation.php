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
use flat_navigation;
use flat_navigation_node;
use stdClass;
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
abstract class navigation  {

    const TEMPLATE_HEADER_DEFAULT = 'theme_cbe/header/header';
    const TEMPLATE_LAYOUT_DEFAULT = 'theme_cbe/columns2/columns2_admin';

    /** @var array Templates Header */
    protected $templates_header = [];

    /**
     * Get Templates.
     *
     * @return array|mixed|string[]
     */
    protected function get_templates(): array {
        return $this->templates_header;
    }

    /**
     * Get Template Header.
     *
     * @return string
     */
    public function get_template_header(): string {
        if (isset($this->get_templates()[$this->get_page()])) {
            return $this->get_templates()[$this->get_page()];
        } else {
            return self::TEMPLATE_HEADER_DEFAULT;
        }
    }

    /**
     * Get Template Layout.
     *
     * @return string
     */
    abstract public function get_template_layout(): string;

    /**
     * Get Data Header.
     *
     * @param stdClass $data
     * @return stdClass
     */
    abstract public function get_data_header(stdClass $data): stdClass;

    /**
     * Get Data Header.
     *
     * @param array $data
     * @return array
     */
    abstract public function get_data_layout(array $data): array;

    /**
     * Get Navigation Page.
     *
     * @return string
     */
    abstract protected function get_page(): string;

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
     * Get Flatnav.
     *
     * @return flat_navigation
     */
    static function get_flatnav(): flat_navigation {
        global $PAGE;

        /** @var flat_navigation $nav */
        $nav = $PAGE->flatnav;

        $nav->remove('contentbank');
        $nav->remove('privatefiles');

        //echo "<pre>";

        //foreach ($nav->getIterator() as $item) {
        //    /** @var flat_navigation_node $item */
        //    $item = $item;
        //    if ($item->key === 'coursehome') {
        //        var_dump($item);die();
        //    }
        //}

        //var_dump($nav->get_key_list());die();

        return $nav;
    }

}
