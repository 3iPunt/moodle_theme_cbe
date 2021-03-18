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
 * @package     theme_cbe
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

namespace theme_cbe\external;

use dml_exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class coursecard_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function courseextra_parameters(): external_function_parameters {
        return new external_function_parameters(
            ['course_id' => new external_value(PARAM_INT, 'Course ID')]
        );
    }

    /**
     * @param string $username
     * @return bool[]|false[]
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function courseextra(string $username): array {
        global $DB;
        $params = self::validate_parameters(
            self::courseextra_parameters(), [
                'username' => $username
            ]
        );

        return [
            'teachers' => [],
            'role' => 'teacher',
            'students_num' => 3
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function courseextra_returns() {
        return null;
    }
}