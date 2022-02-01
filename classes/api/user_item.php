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
 * Class user_item
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
 * Class user_item
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_item  {

    /** @var string Short Name */
    public $shortname;

    /** @var string Icon FontAwesome */
    public $icon;

    /** @var string HREF  */
    public $href;

    /** @var string Name */
    public $name;

    /**
     * constructor.
     *
     * @param string $name
     * @param string $shortname
     * @param string $icon
     * @param string $href
     */
    public function __construct(string $name, string $shortname, string $icon, string $href) {
        $this->name = $name;
        $this->shortname = $shortname;
        $this->icon = $icon;
        $this->href = $href;
    }
}