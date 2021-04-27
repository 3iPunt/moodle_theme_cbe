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
 * Class virtualclasses_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\tables;

use cm_info;
use coding_exception;
use dml_exception;
use theme_cbe\output\module_component;
use theme_cbe\output\user_component;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once('../../lib/tablelib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');

/**
 * Class virtualclasses_table
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class virtualclasses_table extends table_sql {

    /** @var int Course ID */
    protected $course_id;

    /**
     * virtualclasses_table constructor.
     *
     * @param int $course_id
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function __construct(int $course_id) {
        $uniqueid = time();
        parent::__construct($uniqueid);

        $this->course_id = $course_id;

        $this->pageable(true);
        $this->collapsible(true);
        $this->sortable(true);
        $url = '/theme/cbe/view_virtualclasses.php';
        $params_url = ['id' => $course_id];
        $moodle_url = new moodle_url($url, $params_url);
        $this->define_baseurl($moodle_url);

        $this->define_columns([
            'name', 'timecreated', 'openingtime', 'closingtime', 'moderators'
        ]);

        $this->define_headers([
            get_string('vclasses_table_name', 'theme_cbe'),
            get_string('vclasses_table_timecreated', 'theme_cbe'),
            get_string('vclasses_table_openingtime', 'theme_cbe'),
            get_string('vclasses_table_closingtime', 'theme_cbe'),
            get_string('vclasses_table_moderators', 'theme_cbe'),
        ]);

        $this->is_downloadable(false);

        //$this->sortable(true, 'timecreated', SORT_DESC);

        //$this->set_sortdata();
        $this->is_collapsible = false;

        $this->no_sorting('moderators');

        $this->column_style('name', 'text-align', 'left');
        $this->column_style('timecreated', 'text-align', 'left');
        $this->column_style('openingtime', 'text-align', 'left');
        $this->column_style('closingtime', 'text-align', 'left');
        $this->column_style('moderators', 'text-align', 'left');
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
        global $DB;
        $course = get_course($this->course_id);
        $data = [];

        $fastmodinfo = get_fast_modinfo($course);
        /** @var cm_info[] $cms */
        $cms = $fastmodinfo ? $fastmodinfo->get_cms() : [];

        foreach ($cms as $cm) {
            if ($cm->modname === 'bigbluebuttonbn') {
                $instance = $DB->get_record(
                    'bigbluebuttonbn', array('id' => $cm->instance), '*', MUST_EXIST);
                $participants = json_decode($instance->participants);

                $moderatorids = [];
                $viewerids = null;
                foreach ($participants as $item) {
                    if ($item->role === 'moderator') {
                        $moderatorids[] = $item->selectionid;
                    }
                    if ($item->role === 'viewer') {
                        $viewerids[] = $item->selectionid;
                    }
                }

                $row = new stdClass();
                $row->id = $cm->id;
                $row->name = $cm->name;
                $row->openingtime = $instance->openingtime;
                $row->closingtime = $instance->closingtime;
                $row->timecreated = $instance->timecreated;
                $row->moderators = $moderatorids;
                $row->participants = $viewerids;
                $data[] = $row;
            }
        }
        return $data;
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
     * Col openingtime
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_openingtime(stdClass $row): string {
        return $row->openingtime ? userdate($row->openingtime,'%d %B %Y - %H:%M') : '-';
    }

    /**
     * Col closingtime
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_closingtime(stdClass $row): string {
        return $row->closingtime ? userdate($row->closingtime,'%d %B %Y - %H:%M') : '-';
    }

    /**
     * Col timecreated
     *
     * @param stdClass $row Full data of the current row.
     * @return string
     */
    public function col_timecreated(stdClass $row): string {
        return $row->timecreated ? userdate($row->timecreated,'%d %B %Y - %H:%M') : '-';
    }

    /**
     * Col moderators
     *
     * @param stdClass $row
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function col_moderators(stdClass $row): string {
        global $PAGE;
        $html = '';
        foreach ($row->moderators as $moderator) {
            if ($moderator !== 'all') {
                $output = $PAGE->get_renderer('theme_cbe');
                $page = new user_component($moderator, $this->course_id);
                $html .= $output->render($page);
            } else {
                $html .= '<span class="space"></span>' .
                 get_string('all_participants', 'theme_cbe') .
                    '<span class="space"></span>';
            }

        }
        return $html === '' ? '-' : '<div class="moderators">' . $html . '</div>';

    }
}
