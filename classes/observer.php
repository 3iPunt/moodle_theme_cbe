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
 * Class Observer theme_cbe
 *
 * @package     theme_cbe
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

use core\event\course_created;

defined('MOODLE_INTERNAL') || die();


global $CFG;

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Class Event observer for theme_cbe_observer.
 *
 * @package     theme_cbe
 * @copyright   3iPunt <https://www.tresipunt.com/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class theme_cbe_observer {

    /**
     * Evento que controla la creación del curso.
     *
     * @param course_created $event
     * @return bool
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function course_created(course_created $event): bool {

        $courseid = $event->courseid;
        // Virtual Class.
        if (get_config('theme_cbe', 'vclasses_direct')) {
            $modules = get_coursemodules_in_course('bigbluebuttonbn', $courseid);
            $hasmain = false;
            foreach ($modules as $mod) {
                if ($mod->idnumber === 'MAIN') {
                    $hasmain = true;
                    break;
                }
            }
            if (!$hasmain) {
                $moduleinfo = new stdClass();
                $moduleinfo->modulename = 'bigbluebuttonbn';
                $moduleinfo->section = 0;
                $moduleinfo->type = 0;
                $moduleinfo->participants = '';
                $moduleinfo->course = $courseid;
                $moduleinfo->cmidnumber = 'MAIN';
                $moduleinfo->welcome = '';
                $draftid_editor = file_get_submitted_draft_itemid('introeditor');
                file_prepare_draft_area(
                    $draftid_editor, null, null, null, null, array('subdirs'=>true));
                $moduleinfo->introeditor = array('text'=> '', 'format'=> FORMAT_HTML, 'itemid'=>$draftid_editor);
                $moduleinfo->record = 0;
                $moduleinfo->name = get_string('bbb_main', 'theme_cbe');
                $moduleinfo->visible = true;
                try {
                    create_module($moduleinfo);
                } catch (moodle_exception $e) {
                    error_log($e->getMessage());
                }
            }
        }

        // Create Section Default
        /** @var course_modinfo $modinfo*/
        $modinfo = get_fast_modinfo($courseid);
        $sections = $modinfo->get_section_info_all();
        if (count($sections) < 2) {
            // Create.
            $course = get_course($courseid);
            $newsection = course_create_section($courseid, 1);
            $name = get_string('sectionname', 'format_'.
                    $course->format) . ' ' . $newsection->section;
            course_update_section($courseid, $newsection, array('name' => $name));
        }

        return true;
    }

}