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
 * Class publication
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe;

use cm_info;
use coding_exception;
use comment;
use comment_exception;
use context_module;
use dml_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/comment/lib.php');

/**
 * Class publication
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class publication  {

    const MODULE_PUBLICATION = 'tresipuntshare';

    /** @var int CM ID */
    protected $cm_id;

    /** @var cm_info Course Module Moodle */
    protected $cm;

    /** @var stdClass Publication instance */
    protected $instance;

    /** @var stdClass Course Moodle */
    protected $coursemoodle;

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
     * Get Course Moodle.
     *
     * @return cm_info
     */
    public function get_cm(): cm_info {
        return $this->cm;
    }

    /**
     * Get Course Moodle.
     *
     * @return stdClass
     */
    public function get_course(): stdClass {
        return $this->coursemoodle;
    }

    /**
     * Get Intro.
     *
     * @return string
     * @throws dml_exception
     */
    public function get_intro(): string {
        if (isset($this->get_instance()->intro)) {
            $intro = $this->get_instance()->intro;
            return str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br />", $intro);
        } else {
            return '';
        }
    }

    /**
     * Get Teacher.
     *
     * @return string
     * @throws dml_exception
     */
    public function get_teacher(): string {
        if (isset($this->get_instance()->teacher)) {
            return $this->get_instance()->teacher;
        } else {
            return '';
        }
    }


    /**
     * Get Instance.
     * @return stdClass|null
     * @throws dml_exception
     */
    protected function get_instance(): ?stdClass {
        if (empty($this->instance)) {
            $this->set_instance();
        }
        return $this->instance ? $this->instance : null;
    }

    /**
     * Set Instance.
     *
     * @throws dml_exception
     */
    protected function set_instance() {
        global $DB;
        $res = $DB->get_record(self::MODULE_PUBLICATION, array('id'=>$this->cm->instance));
        if ($res) {
            $this->instance = $res;
        }
    }

    /**
     * Get comments.
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws comment_exception
     */
    public function get_comments(): array {
        global $USER;

        $args = new stdClass();
        $args->context = context_module::instance($this->cm_id);
        $args->courseid = $this->coursemoodle->id;
        $args->cm = $this->cm;
        $args->area = 'tresipuntshare';
        $args->itemid = $this->cm_id;
        $args->component = 'mod_tresipuntshare';
        $manager_comment = new comment($args);

        $comments = $manager_comment->get_comments('', 'ASC');

        $data = [];
        foreach ($comments as $comment) {
            $author_cbe = new user($comment->userid);
            $author = $author_cbe->export();

            $can_delete = false;
            $can_edit = false;

            if (course_user::is_teacher($this->cm->course)) {
                $can_delete = true;
            }

            if ((int)$author->id === (int)$USER->id) {
                $can_delete = true;
                $can_edit = true;
            }

            $avatar_api = get_config('theme_cbe', 'avatar_api');
            if ($avatar_api) {
                // TODO. Picture
                $authorpictureurl = $author->picture;
            } else {
                $authorpictureurl = $author->picture;
            }

            $comment = [
                'id' => $comment->id,
                'comment_id' => $comment->id,
                'user_picture' => $authorpictureurl,
                'user_is_connected' => $author->is_connected,
                'fullname' => $author->fullname,
                'date' => userdate($comment->timecreated),
                'timecreated' => $comment->timecreated,
                'text' => $comment->content,
                'textstript' => strip_tags($comment->content),
                'can_edit' => $can_edit,
                'can_delete' => $can_delete
            ];

            $data[] = $comment;
        }
        return $data;
    }
}
