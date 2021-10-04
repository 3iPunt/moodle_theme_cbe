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
 * user
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();


/**
 * user
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {

    /** @var string[] Data */
    public $data;

    /** @var string Account */
    public $account;

    /** @var string Avatar */
    public $avatar;

    /** @var string Password */
    public $password;

    /**
     * constructor.
     *
     * @param string[] $data
     * @throws moodle_exception
     */
    public function __construct(array $data) {
        $this->data = $data;
        $this->set('account');
        $this->set('avatar');
    }

    /**
     * Set.
     *
     * @param string $prop
     * @throws moodle_exception
     */
    protected function set(string $prop) {
        if (isset($this->data[$prop])) {
            $this->{$prop} = $this->data[$prop];
        } else {
            throw new moodle_exception('No se ha encontrado link de usuario ' . $prop);
        }
    }

}
