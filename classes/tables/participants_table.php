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
 * Class participants_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\tables;

use coding_exception;
use course_enrolment_manager;
use dml_exception;
use lang_string;
use theme_cbe\output\user_component;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once('../../lib/tablelib.php');
require_once($CFG->dirroot . '/enrol/themelib.php');

/**
 * Class participants_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_table extends table_sql {

    /** @var int Course ID */
    protected $course_id;

    /**
     *
     * participants_table constructor.
     *
     * @param int $course_id
     * @throws moodle_exception
     */
    public function __construct(int $course_id) {
        $uniqueid = time();
        parent::__construct($uniqueid);

        $this->course_id = $course_id;

        $this->pageable(true);
        $this->collapsible(true);
        $this->sortable(true);
        $url = '/theme/cbe/view_participants.php';
        $params_url = ['id' => $course_id];
        $moodle_url = new moodle_url($url, $params_url);
        $this->define_baseurl($moodle_url);

        $this->define_columns([
            'name', 'email', 'lastcourseaccess', 'groups'
        ]);
        $this->define_headers([
            'Nombre', 'E-mail', 'Último acceso', 'Grupos'
        ]);

        $this->is_downloadable(false);
        $this->is_collapsible = false;

        $this->sortable(true);
        $this->column_style('name', 'text-align', 'left');
        $this->column_style('email', 'text-align', 'left');
    }

    /**
     * Query DB.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @throws dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $this->rawdata = $this->get_data();
    }

    /**
     * Get Data
     *
     * @return array
     * @throws dml_exception
     */
    public function get_data(): array {
        global $PAGE, $DB;
        $course = get_course($this->course_id);
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $enrolmanager = new course_enrolment_manager($PAGE, $course, $instancefilter = null, $role->id,
            $searchfilter = '', $groupfilter = 0, $statusfilter = -1);
        $students = $enrolmanager->get_users(
            'u.lastname, u.firstname, u.id, u.email', 'ASC', 0, 0
        );

        $data = [];
        foreach ($students as $student) {
            $row = new stdClass();
            $row->id = $student->id;
            $row->firstname = $student->firstname;
            $row->lastname = $student->lastname;
            $row->email = $student->email;
            $row->lastaccess = $student->lastaccess;
            $data[] = $row;

        }
        return $data;
    }

    /**
     * Get Role Name.
     *
     * @param string $role
     * @return lang_string|string
     * @throws coding_exception
     */
    public function get_role_name(string $role) {
        switch ($role) {
            case 'manager':
                return get_string('manager', 'role');
            case 'coursecreator':
                return get_string('coursecreators');
            case 'editingteacher':
                return get_string('defaultcourseteacher');
            case 'teacher':
                return get_string('noneditingteacher');
            case 'student':
                return get_string('defaultcoursestudent');
            case 'guest':
                return get_string('guest');
            case 'user':
                return get_string('authenticateduser');
            case 'frontpage':
                return get_string('frontpageuser', 'role');
            default:
                return '';
        }
    }

    /**
     * Col Roles.
     *
     * @param stdClass $row
     * @return string
     * @throws coding_exception
     */
    public function col_roles(stdClass $row): string {
        $context = \context_course::instance($this->course_id);
        $roles = get_user_roles($context, $row->id);
        $userroles = '';

        foreach ($roles as $role) {
            $rolename = $this->get_role_name($role->shortname);

            if (end($roles) !== $role) {
                $userroles .= "$rolename, ";
            } else {
                $userroles .= $rolename;
            }
        }
        return $userroles;
    }

    /**
     * Column Name.
     *
     * @param $row
     * @return bool|string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function col_name($row) {
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $page = new user_component($row->id, $this->course_id);

        return $output->render($page);
    }

    /**
     * Col Email
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_email(stdClass $row): string {
        return $row->email;
    }

    /**
     * Col Last Courseaccess
     *
     * @param stdClass $row
     * @return string
     * @throws coding_exception
     */
    public function col_lastcourseaccess(stdClass $row): string {
        if (!empty($row->lastaccess)) {
            $lastaccess = time() - $row->lastaccess;
            if ($lastaccess < 900) {
                return ucfirst(get_string('now', 'core'));
            }
            return format_time($lastaccess);
        } else {
            return get_string('never');
        }
    }

    /**
     * Col Groups.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_groups(stdClass $row): string {
        $usergroupsids = groups_get_user_groups($this->course_id, $row->id)[0];
        $usergroupsnames = '';
        foreach ($usergroupsids as $usergroupid) {
            $groupname = groups_get_group_name($usergroupid);

            if (end($usergroupsids) !== $usergroupid) {
                $usergroupsnames .= $groupname . ', ';
            } else {
                $usergroupsnames .= $groupname;
            }
        }
        return $usergroupsnames;
    }
}
