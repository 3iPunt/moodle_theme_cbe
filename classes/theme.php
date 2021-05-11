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
 * Class theme
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use coding_exception;
use dml_exception;
use moodle_exception;
use moodle_url;
use section_info;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class theme
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme  {

    /** @var section_info Section */
    protected $section;

    /** @var stdClass Course */
    protected $course;

    /**
     * constructor.
     *
     * @param section_info|null $section
     * @param stdClass|null $course
     * @throws dml_exception
     */
    public function __construct(section_info $section, stdClass $course = null) {
        $this->section = $section;
        if (isset($course)) {
            $this->course = $course;
        } else {
            $this->course = get_course($section->course);
        }
    }

    /**
     * Export module for render.
     *
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export(): stdClass {
        $section = new stdClass();
        $section->id = $this->section->id;
        $section->is_hidden = !$this->section->visible;
        $section->section = $this->section->section;
        $section->name = $this->get_name();
        $section->href = $this->get_href();
        return $section;
    }

    /**
     * Get Name
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name(): string {
        if (is_null($this->section->name)) {
            $name = get_string('sectionname', 'format_'.
                    $this->course->format) . ' ' . $this->section->section;
        } else {
            $name = $this->section->name;
        }
        return $name;
    }

    /**
     * Get HRef
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_href(): string {
        $href = new moodle_url('/course/view.php', [
            'id'=> $this->course->id, 'section' => $this->section->section
        ]);
        return $href->out(false);
    }

}
