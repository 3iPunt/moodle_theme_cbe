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
 * user_menu
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use coding_exception;
use dml_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();


/**
 * user_menu
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_menu {

    /** @var string[] Data */
    public $data;

    /** @var user_item[] User Items */
    public $items;

    /**
     * constructor.
     *
     * @param string[][] $data
     * @throws moodle_exception
     */
    public function __construct(array $data) {
        $this->data = $data;
        foreach ($this->data as $item) {
            $name = $this->set($item, 'name');
            $shortname = $this->set($item, 'shortname');
            $icon = $this->set($item, 'icon');
            $href = $this->set($item, 'href');
            $user_item = new user_item($name, $shortname, $icon, $href);
            $this->items[] = $user_item;
        }
    }

    /**
     * Set.
     *
     * @param array $item
     * @param string $prop
     * @return string
     * @throws moodle_exception
     */
    protected function set(array $item, string $prop): string {
        if (isset($item[$prop])) {
            return $item[$prop];
        } else {
            throw new moodle_exception('No se ha encontrado propiedad del Menú de usuario: ' . $prop);
        }
    }

}
