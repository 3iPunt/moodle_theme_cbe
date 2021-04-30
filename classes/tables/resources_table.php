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
 * Class resources_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\tables;

use cm_info;
use coding_exception;
use core_filetypes;
use dml_exception;
use theme_cbe\output\module_component;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once('../../lib/tablelib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class resources_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resources_table extends table_sql {

    /** @var int Course ID */
    protected $course_id;

    /** @var int User ID */
    protected $user_id;

    /**
     * students_in_course_table constructor.
     *
     * @param int $course_id
     * @param int $user_id
     * @throws moodle_exception
     */
    public function __construct(int $course_id, int $user_id) {
        $uniqueid = time();
        parent::__construct($uniqueid);

        $this->course_id = $course_id;
        $this->user_id = $user_id;

        $this->pageable(true);
        $this->collapsible(true);
        $this->sortable(true);
        $url = '/theme/cbe/view_resources.php';
        $params_url = ['id' => $course_id];
        $moodle_url = new moodle_url($url, $params_url);
        $this->define_baseurl($moodle_url);

        $this->define_columns([
            'name', 'filename', 'type', 'size'
        ]);
        $this->define_headers([
            get_string('course_left_resources', 'theme_cbe'),
            get_string('filename', 'theme_cbe'),
            get_string('type', 'theme_cbe'),
            get_string('size', 'theme_cbe')
        ]);

        $this->is_downloadable(false);
        $this->is_collapsible = false;

        $this->sortable(true, 'duedate');

        $this->column_style('name', 'text-align', 'left');
    }

    /**
     * Query DB.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $this->rawdata = $this->get_data();
    }

    /**
     * Get Data
     *
     * @return array
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_data(): array {
        $course = get_course($this->course_id);
        $data = [];

        $fastmodinfo = get_fast_modinfo($course);
        /** @var cm_info[] $cms */
        $cms = $fastmodinfo ? $fastmodinfo->get_cms() : [];

        foreach ($cms as $cm) {
            if ($cm->modname === 'resource' ||
                $cm->modname === 'tresipuntvideo' ||
                $cm->modname === 'tresipuntaudio') {
                $row = new stdClass();
                $row->id = $cm->id;
                $row->section = $cm->sectionnum;
                $row->name = $cm->name;
                $row->modname = $cm->modname;
                // Get File.
                $fs = get_file_storage();
                $files = $fs->get_area_files($cm->context->id, 'mod_' . $cm->modname, 'content');
                foreach ($files as $file) {
                    if (!empty($file->get_mimetype())) {
                        $row->filename = $file->get_filename();
                        $row->type = $file->get_mimetype();
                        $row->size = $file->get_filesize();
                        break;
                    }
                }


                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Col section
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_section(stdClass $row): string {
        return get_section_name($this->course_id, $row->section);
    }

    /**
     * Col name
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_name(stdClass $row): string {
        global $PAGE;
        $output = $PAGE->get_renderer('theme_cbe');
        $page = new module_component($row->id);

        return $output->render($page);
    }

    /**
     * Col filename
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_filename(stdClass $row): string {
        return !empty($row->filename) ? $row->filename : '-';
    }

    /**
     * Col type
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     * @throws coding_exception
     */
    public function col_type(stdClass $row): string {
        return !empty($row->type) ? core_filetypes::get_file_extension($row->type) : '-';
    }

    /**
     * Col size
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_size(stdClass $row): string {
        return !empty($row->type) ? display_size($row->size) : '-';
    }

}
