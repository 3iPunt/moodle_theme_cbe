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
 * apps
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use coding_exception;
use dml_exception;
use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();


/**
 * apps
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class apps {

    /** @var string[] Data */
    public $data;

    /** @var app[] Apps */
    public $apps;

    /**
     * constructor.
     *
     * @param string[][] $data
     * @param bool $is_external
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function __construct(array $data, bool $is_external) {
        $this->data = $data;
        foreach ($this->data as $item) {
            $shortname = $this->set($item, 'shortname');
            $icon = $this->set($item, 'icon');
            $href = $this->set($item, 'href');
            $name = $this->set($item, 'name');
            if ($shortname === 'courses') {
                $is_external = false;
                $home = new moodle_url('/');
                $href = $home->out(false);
            }
            $app = new app($shortname, $icon, $href, $is_external, $name);
            $this->apps[] = $app->get();
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
            throw new moodle_exception('No se ha encontrado propiedad del APP ' . $prop);
        }
    }

}
