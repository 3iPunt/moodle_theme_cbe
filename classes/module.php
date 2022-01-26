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
 * Class module
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use cm_info;
use coding_exception;
use comment_exception;
use context_module;
use core_course\local\factory\content_item_service_factory;
use core_course_external;
use core_media_manager;
use dml_exception;
use moodle_exception;
use moodle_url;
use pix_icon;
use stdClass;

global $CFG;
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/lib/modinfolib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class module
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module  {

    /** @var int Course Module ID */
    protected $cm_id;

    /** @var cm_info Course Module Moodle*/
    protected $cm;

    /** @var stdClass Course Moodle*/
    protected $coursemoodle;

    /** @var string[] Activities */
    static public $activities = ['assign', 'forum', 'quiz', 'feedback', 'bigbluebuttonbn', 'h5pactivity'];

    /** @var string[] Resources */
    static public $resources = ['tresipuntvideo', 'tresipuntaudio', 'resource', 'folder', 'url', 'page'];

    /** @var string[] Others */
    static public $others = ['label', 'tresipuntshare'];

    /**
     * constructor.
     *
     * @param int $cm_id
     * @throws moodle_exception
     */
    public function __construct(int $cm_id) {
        $this->cm_id = $cm_id;
        list($this->coursemoodle, $this->cm) = get_course_and_cm_from_cmid($cm_id);
    }

    /**
     * Get List Modname.
     *
     * @return string[]
     */
    static public function get_list_modname(): array {
        $merge = array_merge(self::$activities, self::$resources);
        array_push($merge, publication::MODULE_PUBLICATION);
        return $merge;
    }

    /**
     * Get CM Info.
     *
     * @return cm_info
     */
    public function get_cm_info(): cm_info {
        return $this->cm;
    }

    /**
     * Export module for render.
     *
     * @param bool $board Is Board logical?
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function export(bool $board = false): stdClass {
        $module = new stdClass();
        $module->id = $this->cm_id;
        $module->modname = $this->get_modname();
        $module->icon = $this->get_icon();
        $module->html_icon = $this->get_html_icon();
        $module->modfullname = $this->get_modfullname();
        $module->name = $this->get_name();
        $module->added = $this->get_added();
        $module->updated = $this->get_updated();
        $module->is_hidden = $this->is_hidden();
        $module->is_publication = false;
        $module->view_href = $this->get_view_href();
        $module->view_blank = false;
        $module->theme = $this->get_theme();
        $module->edit_href = $this->get_edit_href();
        $module->is_resource = $this->is_resource();
        $module->is_media = false;
        $module->is_mine = false;
        $module->can_deleted = $this->can_deleted();
        $module->sectionname = get_section_name($this->coursemoodle->id, $this->cm->sectionnum);
        if ($board) {
            switch ($this->cm->modname) {
                case publication::MODULE_PUBLICATION:
                    $module = $this->set_publication($module);
                    break;
                case 'url':
                    $module = $this->set_url($module);
                    $module = $this->set_description($module);
                    break;
                case 'resource':
                    $module = $this->set_resource($module);
                    $module = $this->set_description($module);
                    break;
                case 'tresipuntvideo':
                case 'tresipuntaudio':
                    $module = $this->set_media($module);
                    break;
                default:
                    $module = $this->set_description($module);
            }
        }
        return $module;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function get_name(): string {
        return $this->cm->name;
    }

    /**
     * Is Hidden?
     *
     * @return string
     */
    public function is_hidden(): string {
        return !$this->cm->visible;
    }

    /**
     * Is Resource?
     *
     * @return bool
     */
    public function is_resource(): bool {
        return in_array($this->cm->modname, self::$resources);
    }

    /**
     * Get Modname
     *
     * @return string
     */
    public function get_modname(): string {
        return $this->cm->modname;
    }

    /**
     * Get Icon
     *
     * @return string
     */
    public function get_icon(): string {
        return $this->cm->get_icon_url();
    }

    /**
     * Get Icon
     *
     * @return string
     * @throws coding_exception
     */
    public function get_html_icon(): string {
        global $PAGE;
        if (in_array($this->cm->modname, module::$resources)) {
            $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
            $classname = 'theme_cbe\output\module_' . $this->cm->modname . '_icon_component';
            $module_resource_icon_component = new $classname($this->cm);
            return $output_theme_cbe->render($module_resource_icon_component);
        } else {
            return '<img class="icon" src="' . $this->cm->get_icon_url() . '" alt="">';
        }
    }

    /**
     * Get Module Fullname
     *
     * @return string
     */
    public function get_modfullname(): string {
        return $this->cm->modfullname;
    }

    /**
     * Get Added
     *
     * @return string
     */
    public function get_added(): string {
        return userdate($this->cm->added);
    }

    /**
     * Get Updated
     *
     * @return string
     */
    public function get_updated(): string {
        return $this->cm->added;
    }

    /**
     * Get Theme
     *
     * @return string
     */
    public function get_theme(): string {
        if (!empty($this->cm->get_section_info()->name)) {
            return $this->cm->get_section_info()->name;
        } else {
            return '';
        }
    }

    /**
     * Get View HRef
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_view_href(): string {
        return new moodle_url('/mod/' . $this->cm->modname. '/view.php', ['id'=> $this->cm->id]);
    }

    /**
     * Get Edit HRef
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_edit_href(): string {
        return new moodle_url('/course/modedit.php', ['update'=> $this->cm->id]);
    }

    /**
     * Set Publication.
     *
     * @param $module
     * @return mixed
     * @throws moodle_exception
     */
    protected function set_publication($module) {
        global $USER;
        $publication = new publication($this->cm->id);
        $module->is_publication = true;
        $module->comment = $publication->get_intro();
        $author_id = $publication->get_teacher();
        if (!empty($author_id)) {
            $author_cbe = new user($author_id);
            $author = $author_cbe->export();
            $module->author = $author;
        }
        $module->comments = $publication->get_comments();
        $module->has_comments = count($publication->get_comments()) > 0;
        $module->moderestriction = $this->get_restriction()->mode;
        $module->msgrestriction = $this->get_restriction()->msg;
        if ($author_id === $USER->id) {
            $module->is_mine = true;
        }
        return $module;
    }

    /**
     * Get Restriction.
     *
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function get_restriction(): stdClass {
        $availability = $this->cm->availability;
        $mode = '';
        $msg = '';
        if (empty($availability)) {
            $mode = 'all';
            $msg = get_string('rest_all_student', 'theme_cbe');
        } else {
            $availability = json_decode($availability);
            if (isset($availability->c)) {
                $c = current($availability->c);
                if ($c->type === 'group') {
                    $group = groups_get_group($c->id);
                    if ($group) {
                        $mode = 'group';
                        $msg = get_string('rest_group', 'theme_cbe') . ' ' . $group->name;
                    }
                } else if ($c->type === 'profile') {
                    if ($c->sf === 'email') {
                        $user = \core_user::get_user_by_email($c->v);
                        if ($user) {
                            $mode = 'student';
                            if (course_user::is_teacher($this->coursemoodle->id)) {
                                $msg = get_string('rest_student', 'theme_cbe') . ' ' . fullname($user);
                            } else {
                                $msg = get_string('rest_student_my', 'theme_cbe');
                            }
                        }
                    }
                }
            }
        }
        $rest = new stdClass();
        $rest->mode = $mode;
        $rest->msg = $msg;
        return $rest;
    }

    /**
     * Set URL.
     *
     * @param $module
     * @return mixed
     * @throws moodle_exception
     */
    protected function set_url($module) {
        global $DB;
        $url_module = $DB->get_record('url', array('id'=>$this->cm->instance));
        $module->view_href = $url_module->externalurl;
        $module->view_blank = true;
        return $module;
    }

    /**
     * Can deleted?
     *
     * @return bool
     */
    public function can_deleted(): bool {
        return !(($this->cm->modname === 'bigbluebuttonbn') && ($this->cm->idnumber === 'MAIN'));
    }

    /**
     * Set Resource.
     *
     * @param $module
     * @return mixed
     */
    protected function set_resource($module) {
        $module->view_blank = true;
        return $module;
    }

    /**
     * Set Media.
     *
     * @param $module
     * @return mixed
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function set_media($module) {
        $module->is_media = true;
        $module->media = $this->get_media($this->cm->modname);
        return $module;
    }

    /**
     * Set Description.
     *
     * @param $module
     * @return mixed
     * @throws dml_exception
     */
    protected function set_description($module) {
        global $DB;
        if ($this->cm->showdescription) {
            $instance = $DB->get_record($this->cm->modname, ['id' => $this->cm->instance]);
            if (isset($instance)) {
                $module->is_description = true;
                $module->description = $instance->intro;
            }
        }
        return $module;
    }

    /**
     * Get Media
     *
     * @param $modname
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function get_media($modname): string {
        global $DB, $PAGE;
        $context = context_module::instance($this->cm_id);

        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $context->id, 'mod_' . $modname,
            'content', 0,
            'sortorder DESC, id ASC', false);

        $file = reset($files);

        $video = $DB->get_record($modname, array('id'=>$this->cm->instance));

        $moodleurl = moodle_url::make_pluginfile_url(
            $context->id, 'mod_' . $modname, 'content', $video->revision,
            $file->get_filepath(), $file->get_filename());

        $embedoptions = array(
            core_media_manager::OPTION_TRUSTED => true,
            core_media_manager::OPTION_BLOCK => true,
        );

        $mediamanager = core_media_manager::instance($PAGE);

        return $mediamanager->embed_url(
            $moodleurl, $this->cm->name, 0, 0, $embedoptions
        );
    }

    /**
     * Get List Modules.
     *
     * @param int $course_id
     * @param int $in_section
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_list_modules(int $course_id, int $in_section = 1): array {
        global $USER;

        $favourites = [];
        $recommended = [];
        $activities = [];
        $resources = [];

        $course = get_course($course_id);
        $contentitemservice = content_item_service_factory::get_content_item_service();

        $items = $contentitemservice->get_content_items_for_user_in_course($USER, $course);

        foreach ($items as $item) {
            if ($item->name !== 'label' && $item->name !== 'tresipuntshare') {

                $mod = self::get_mod($course_id, $item->name, $in_section);
                $mod['is_resource'] = false;

                if ($item->archetype === 0) {
                    $activities[] = $mod;
                } else {
                    $mod['is_resource'] = true;
                    $resources[] = $mod;
                }

                if ($item->favourite) {
                    $favourites[] = $mod;
                }
                if ($item->recommended) {
                    $recommended[] = $mod;
                }
            }
        }

        return [
            'has_favourites' => count($favourites) > 0,
            'favourites' => $favourites,
            'recommended' => $recommended,
            'activities' => $activities,
            'resources' => $resources,
        ];
    }

    /**
     * Get Mod for creation.
     *
     * @param int $course_id
     * @param string $modname
     * @param int $in_section
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    static public function get_mod(int $course_id, string $modname, int $in_section): array {
        global $OUTPUT;
        $params = [
            'add' => $modname,
            'type' => '',
            'course' => $course_id,
            'section' => $in_section,
            'return' => 0,
            'sr' => 0
        ];
        $url = new moodle_url('/course/modedit.php', $params);

        if (in_array($modname, module::$resources)) {
            global $PAGE;
            $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
            $classname = 'theme_cbe\output\module_' . $modname . '_icon_component';
            $module_resource_icon_component = new $classname();
            $icon = $output_theme_cbe->render($module_resource_icon_component);
        } else {
            $icon = new pix_icon('icon', '', $modname, array('class' => 'icon'));
            $icon = $OUTPUT->render($icon);
        }

        return [
            'mod_url' =>$url->out(false),
            'modname' => $modname,
            'icon' => $icon,
            'modtitle' => get_string('pluginname', 'mod_' . $modname)
        ];
    }

}
