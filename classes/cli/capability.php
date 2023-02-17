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

use context_system;
use dml_exception;
use moodle_exception;

class capability {

    /**
     * Execute.
     *
     * @throws dml_exception
     */
    public static function execute() {
        self::manager();
        self::user();
        self::coursecreator();
        self::centre();
    }

    /**
     * Manager
     *
     * @throws dml_exception
     */
    public static function manager() {
        global $DB;
        $rolename = 'manager';
        $role = $DB->get_record('role', ['shortname' => $rolename]);
        if ($role) {
            cli_writeln('Add Capability: ' . 'not modified' . ' - ' . $rolename);
        }
    }

    /**
     * User Auth
     *
     * @throws dml_exception
     */
    public static function user() {
        global $DB;
        $rolename = 'user';
        $role = $DB->get_record('role', ['shortname' => $rolename]);
        if ($role) {
            self::add('moodle/user:editownprofile', $rolename, $role->id);
            self::remove('moodle/my:manageblocks', $rolename, $role->id);
            self::remove('moodle/user:manageownfiles', $rolename, $role->id);
            self::remove('moodle/user:editownmessageprofile', $rolename, $role->id);
            self::remove('report/usersessions:manageownsessions', $rolename, $role->id);
            self::remove('moodle/user:manageownblocks', $rolename, $role->id);
        }
    }

    /**
     * Course Creator
     *
     * @throws dml_exception
     */
    public static function coursecreator() {
        global $DB;
        $rolename = 'coursecreator';
        $role = $DB->get_record('role', ['shortname' => $rolename]);
        if ($role) {
            self::add('moodle/course:manageactivities', $rolename, $role->id);
            self::add('mod/bigbluebuttonbn:addinstance', $rolename, $role->id);
            self::add('moodle/course:delete', $rolename, $role->id);
            self::add('mod/tresipuntshare:addinstance', $rolename, $role->id);
            self::add('mod/tresipuntshare:view', $rolename, $role->id);
            self::add('mod/assign:viewownsubmissionsummary', $rolename, $role->id);
            self::add('moodle/user:manageownfiles', $rolename, $role->id);
            self::add('repository/user:view', $rolename, $role->id);
            self::add('moodle/category:viewcourselist', $rolename, $role->id);
            self::remove('moodle/course:delete', $rolename, $role->id);
            // Allow role assignments.
            self::assign_role($rolename, $role->id, 'editingteacher');
        }
    }

    /**
     * Course Creator
     *
     * @throws dml_exception
     */
    public static function centre() {
        global $DB;
        $rolename = 'centre';
        $role = $DB->get_record('role', ['shortname' => $rolename]);
        if ($role) {
            cli_writeln('Add Capability: ' . 'not modified' . ' - ' . $rolename);
            cli_writeln('Remove Capability: ' . 'not modified' . ' - ' . $rolename);
            // Allow role assignments.
            self::assign_role($rolename, $role->id, 'coursecreator');
            self::assign_role($rolename, $role->id, 'editingteacher');
            self::assign_role($rolename, $role->id, 'teacher');
            self::assign_role($rolename, $role->id, 'student');
        }
    }

    /**
     * Add Capability.
     *
     * @param $capability
     * @param $rolename
     * @param $roleid
     * @throws dml_exception
     */
    protected static function add($capability, $rolename, $roleid) {
        $contextid = context_system::instance();
        try {
            assign_capability($capability, CAP_ALLOW, $roleid, $contextid);
            cli_writeln('Add Capability: ' . $capability . ' - ' . $rolename);
        } catch (moodle_exception $e) {
            cli_writeln($e->getMessage());
            cli_writeln('Add Capability: ' . $capability . ' - ' . $rolename . '- ERROR');
        }
    }

    /**
     * Remove Capability.
     *
     * @param $capability
     * @param $rolename
     * @param $roleid
     * @throws dml_exception
     */
    protected static function remove($capability, $rolename, $roleid) {
        $contextid = context_system::instance();
        try {
            unassign_capability($capability, $roleid, $contextid);
            cli_writeln('Remove Capability: ' . $capability . ' - ' . $rolename);
        } catch (moodle_exception $e) {
            cli_writeln($e->getMessage());
            cli_writeln('Remove Capability: ' . $capability . ' - ' . $rolename . '- ERROR');
        }
    }

    /**
     * Asignar Role.
     *
     * @param $username
     * @param $roleid
     * @param $roleassign
     */
    protected static function assign_role($username, $roleid, $roleassign) {
        global $DB;
        try {
            $role = $DB->get_record('role', ['shortname' => $roleassign]);
            if ($role) {
                $params = new \stdClass();
                $params->roleid = $roleid;
                $params->allowassign = $role->id;
                $record = $DB->get_record('role_allow_assign', (array)$params);
                if (!$record) {
                    $DB->insert_record('role_allow_assign', $params);
                    cli_writeln('Assign Role: ' . $username . ' to ' . $roleassign);
                } else {
                    cli_writeln('Assign Role: ' . $username . ' to ' . $roleassign . ' - Already assigned!');
                }
            }
        } catch (moodle_exception $e) {
            cli_writeln('Assign Role: ' . $username . '(' . $roleassign . ') ERROR - ' . $e->getMessage());
        }
    }
}
