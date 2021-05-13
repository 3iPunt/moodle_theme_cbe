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
 * Response
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Response
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response {

    /** @var bool Success */
    public $success;

    /** @var response_api Data response */
    public $data;

    /** @var error Error object */
    public $error;

    /**
     * Response constructor.
     * @param bool $success
     * @param response_api|null $data
     * @param error|null $error $error
     */
    public function __construct(bool $success, response_api $data = null, error $error = null) {
        $this->success = $success;
        $this->data = $data;
        if (isset($error)) {
            $this->error = $error;
        } else {
            $this->error = new error(0, '');
        }

    }
}
