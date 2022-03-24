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

global $CFG;

use context_system;
use core_plugin_manager;
use dml_exception;
use repository;
use repository_exception;
use repository_instance_form;
use repository_type;
use stdClass;

require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/repository/lib.php');

defined('MOODLE_INTERNAL') || die();

class nextcloud {

    /**
     * Execute
     *
     * @param string $wwwroot
     * @param string|null $ncadmin
     * @param string|null $ncpass
     * @throws dml_exception
     */
    static public function execute(string $wwwroot, string $ncadmin = null, string $ncpass = null) {
        // Repository NextCloud
        self::repository($wwwroot, $ncadmin, $ncpass);
        // Assign Submission
        self::submission($wwwroot, $ncadmin, $ncpass);
    }

    /**
     * Repository NextCloud.
     *
     * @param string $wwwroot
     * @param string|null $ncadmin
     * @param string|null $ncpass
     * @throws repository_exception
     * @throws dml_exception
     * @throws \coding_exception
     */
    static public function repository(string $wwwroot, string $ncadmin = null, string $ncpass = null) {

        $repositorytype = repository::get_type_by_typename('nextcloud');
        if (empty($repositorytype)) {
            $type = new repository_type('nextcloud');
            $type->create();

            $repositorytype = repository::get_type_by_typename('nextcloud');
            if (empty($repositorytype)) {
                cli_writeln('NextCloud Repository: Error - Not exist!!');
                return null;
            }
            $repositorytype->update_visibility(true);
            core_plugin_manager::reset_caches();
            cli_writeln('NextCloud Repository: Actived');
        } else {
            cli_writeln('NextCloud Repository: Already Active!!');
        }

        // TODO
        // Create oAuth2
        cli_writeln('NextCloud oAuth2: Not configured!!');
        // Create NextCloud Repository Instance.
        //// Problem: Current user don't have this capability: 'moodle/site:config'
        cli_writeln('NextCloud Repository: Not configured!!');
    }

    /**
     * Assign Submission NextCloud.
     *
     * @param string $wwwroot
     * @param string|null $ncadmin
     * @param string|null $ncpass
     * @throws dml_exception
     */
    static public function submission(string $wwwroot, string $ncadmin = null, string $ncpass = null) {
        $index = strpos($wwwroot, 'moodle');
        if ($index !== false) {
            $default_host = substr($wwwroot,$index + 7);
            cfg::set('assignsubmission_tipnc', 'host', 'https://nextcloud.' . $default_host);
        }
        cfg::set('assignsubmission_tipnc', 'user', $ncadmin);
        cfg::set('assignsubmission_tipnc', 'password', $ncpass);
        cfg::set('assignsubmission_tipnc', 'folder', 'tasks');
        cfg::set('assignsubmission_tipnc', 'template', 'template.docx');
        cfg::set('assignsubmission_tipnc', 'location', '/apps/onlyoffice/');
    }

}