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
 * Class module
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use cm_info;
use coding_exception;
use core_availability\info_module;
use core_course\external\course_summary_exporter;
use core_course_category;
use course_enrolment_manager;
use course_modinfo;
use dml_exception;
use mod_assign\plugininfo\assignfeedback;
use mod_quiz\plugininfo\quiz;
use moodle_exception;
use moodle_url;
use stdClass;
use user_picture;

global $CFG;
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/modinfolib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class module
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module  {

    /** @var int Course Module ID */
    protected $cm_id;

    /** @var cm_info Course Module Moodle*/
    protected $cm;

    /** @var stdClass Course Moodle*/
    protected $coursemoodle;

    /**
     * constructor.
     *
     * @param int $cm_id
     * @throws moodle_exception
     */
    public function __construct(int $cm_id) {
        $this->cm_id = $cm_id;
        list($this->coursemoodle, $this->cm) = get_course_and_cm_from_cmid($cm_id);
    }

    /**
     * Get CM Info.
     *
     * @return cm_info
     */
    public function get_cm_info(): cm_info {
        return $this->cm;
    }

    /**
     * Get List Modules.
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_list_modules(int $course_id) {
        return [
            'activities' => self::get_list_activities($course_id),
            'resources' => self::get_list_resources($course_id),
        ];
    }

    /**
     * Get List Activities.
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_list_activities(int $course_id): array {
        $activities = [];
        $activities[] = self::get_mod($course_id, 'assign');
        $activities[] = self::get_mod($course_id, 'forum');
        $activities[] = self::get_mod($course_id, 'quiz');
        $activities[] = self::get_mod($course_id, 'feedback');
        $activities[] = self::get_mod($course_id, 'bigbluebuttonbn');
        return $activities;
    }

    /**
     * Get List Resources.
     *
     * @param int $course_id
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_list_resources(int $course_id): array {
        $activities = [];
        $activities[] = self::get_mod($course_id, 'tresipuntvideo');
        $activities[] = self::get_mod($course_id, 'tresipuntaudio');
        $activities[] = self::get_mod($course_id, 'resource');
        $activities[] = self::get_mod($course_id, 'url');
        return $activities;
    }

    /**
     * Get Mod for creation.
     *
     * @param int $course_id
     * @param string $modname
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_mod(int $course_id, string $modname): array {
        $params = [
            'add' => $modname,
            'type' => '',
            'course' => $course_id,
            'section' => 1,
            'return' => 0,
            'sr' => 0
        ];
        $url = new moodle_url('/course/modedit.php', $params);
        return [
            'mod_url' =>$url->out(false),
            'modname' => $modname,
            'modtitle' => get_string('pluginname', 'mod_' . $modname)
        ];
    }



}