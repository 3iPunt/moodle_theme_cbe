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
 * @copyright   2022 Tresipunt
 */

namespace theme_cbe\cli;

use coding_exception;
use dml_exception;

defined('MOODLE_INTERNAL') || die();

class role {

    /**
     * Execute
     */
    static public function execute() {
        self::centre();
    }

    /**
     * Create rol Centre.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    static public function centre() {
        global $DB;
        $name = 'Centre';
        $shortname = 'centre';
        $description = "El rol del centre podrà gestionar la plataforma sense configuracions d'administrador";
        $archetype = 'manager';
        $rolecentre = $DB->get_record('role', ['shortname' => $shortname]);
        if (empty($rolecentre)) {
            $roleid = create_role($name, $shortname, $description, $archetype);
            cli_writeln('Role: centre - ' . $roleid);
        } else {
            cli_writeln('Role: centre - Already Exist! - ' . $rolecentre->id);
        }
    }


}
