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

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use moodle_exception;
use core_user_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

class blockright_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function set_status_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'action' => new external_value(PARAM_INT, 'Contract: 0, Expand: 1')
            ]
        );
    }

    /**
     * @param int $action
     * @return array
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function set_status(int $action): array {

        global $CFG;

        require_once($CFG->dirroot . '/user/externallib.php');

        self::validate_parameters(
            self::set_status_parameters(), [
                'action' => $action
            ]
        );

        try {
            set_user_preference('theme_cbe_blockright', $action);
            $success = true;
            $error = '';
        } catch (moodle_exception $e) {
            $success = false;
            $error = $e->getMessage();
        }

        return [
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function set_status_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Was it a success?'),
                'error' => new external_value(PARAM_TEXT, 'Error message'),
            )
        );
    }
}
