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


}