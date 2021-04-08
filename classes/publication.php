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
use core_user;
use dml_exception;
use moodle_exception;
use stdClass;
use user_picture;

defined('MOODLE_INTERNAL') || die;

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

    /** @var cm_info Course Module Moodle*/
    protected $cm;

    /** @var stdClass Course Moodle*/
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
     * Get Teacher.
     *
     * @return string
     * @throws dml_exception
     */
    public function get_teacher(): string {
        global $DB;
        $share_module = $DB->get_record(self::MODULE_PUBLICATION, array('id'=>$this->cm->instance));
        if ($share_module) {
            return $share_module->teacher;
        } else {
            return '';
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
    function get_comments(): array {
        global $PAGE;

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
            $author = core_user::get_user($comment->userid);
            $authorpicture = new user_picture($author);
            $authorpicture->size = 1;
            $author_picture = $authorpicture->get_url($PAGE)->out(false);

            $comment = [
                'user_picture' => $author_picture,
                'fullname' => fullname($author),
                'date' => userdate($comment->timecreated),
                'text' => $comment->content
            ];

            $data[] = $comment;
        }
        return $data;
    }
}
