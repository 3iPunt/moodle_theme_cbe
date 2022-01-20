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
 * Class app
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use coding_exception;
use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class app
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class app  {

    /** @var string Short Name */
    protected $shortname;

    /** @var string Icon FontAwesome */
    protected $icon;

    /** @var string HREF  */
    protected $href;

    /** @var bool Is External link? */
    protected $is_external;

    /** @var string Name */
    protected $name;

    /**
     * constructor.
     *
     * @param string $shortname
     * @param string $icon
     * @param string $href
     * @param bool $is_external
     * @param string $name
     * @throws coding_exception
     * @throws dml_exception
     */
    public function __construct(string $shortname, string $icon, string $href, bool $is_external, string $name = '') {
        $this->shortname = $shortname;
        $this->icon = $icon;
        $this->is_external = $is_external;
        $this->set_name($name);
        $this->set_icon($icon);
        $this->set_href($href);
    }

    /**
     * Set Name.
     *
     * @param string $name
     * @throws coding_exception
     */
    protected function set_name(string $name) {
        $string = get_string('app_menu_' . $this->shortname, 'theme_cbe');
        if (!strpos($string, ']]')) {
            $this->name = $string;
        } else {
            $this->name = $name;
        }
    }

    /**
     * Set Icon.
     *
     * @param string $icon
     */
    protected function set_icon(string $icon) {
        $this->icon = $icon;
    }

    /**
     * Set HRef.
     *
     * @param string $href
     * @throws dml_exception
     */
    protected function set_href(string $href) {
        if (!$this->is_external) {
            $host = get_config('theme_cbe', 'host');
            if ($host) {
                $href = str_replace('XXX', $host, $href);
            }
        }
        $this->href = $href;
    }

    /**
     * Get object.
     *
     * @return stdClass
     */
    public function get(): stdClass {
        $object = new stdClass();
        $object->shortname = $this->shortname;
        $object->name = $this->name;
        $object->icon = $this->icon;
        $object->href = $this->href;
        $object->is_external = $this->is_external;
        return $object;
    }

}