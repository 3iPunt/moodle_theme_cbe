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
 * colours
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();


/**
 * colours
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class colours {

    /** @var string[] Data */
    public $data;

    /** @var string Background */
    public $background;

    /** @var string Primary Color */
    public $primary;

    /** @var string Secondary Color */
    public $secondary;

    /**
     * constructor.
     *
     * @param string[] $data
     * @throws moodle_exception
     */
    public function __construct(array $data) {
        $this->data = $data;
        $this->set('background');
        $this->set('primary');
        $this->set('secondary');
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
            throw new moodle_exception('No se ha encontrado color ' . $prop);
        }
    }

}
