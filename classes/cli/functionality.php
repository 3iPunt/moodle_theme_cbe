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

use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

class functionality {

    /**
     * Execute
     * @throws dml_exception
     */
    static public function execute() {
        self::blocks();
    }

    /**
     * Execute
     * @throws dml_exception
     */
    static public function blocks() {
        self::create_block('tresipuntmodspend');
        self::block_position();
    }

    /**
     * Create block
     *
     * @param string $blockname
     * @throws dml_exception
     */
    static protected function create_block(string $blockname) {
        global $DB;
        $tablename = 'block_instances';
        $params = new stdClass;
        $params->blockname = $blockname;
        $params->pagetypepattern = 'my-index';
        $params->defaultregion = 'side-pre';
        $record = $DB->get_record($tablename, (array)$params);
        if (!$record) {
            $params->parentcontextid = 1;
            $params->showinsubcontexts = 0;
            $params->subpagepattern = null;
            $params->defaultweight = 2;
            $params->configdata = '';
            $params->timecreated = time();
            $params->timemodified = time();
            $DB->insert_record($tablename, $params);
            cli_writeln('Block: ' . $blockname);
        }
    }

    /**
     * Position blocks
     *
     * @throws dml_exception
     */
    static protected function block_position() {
        global $DB;
        $tablename = 'block_instances';
        $timeline = new stdClass();
        $timeline->blockname = 'timeline';
        $timeline->pagetypepattern = 'my-index';
        $timeline->defaultregion = 'side-pre';
        $timerecord = $DB->get_record($tablename, (array)$timeline);
        if ($timerecord) {
            $timeline->id = $timerecord->id;
            $timeline->defaultweight = 3;
            $DB->update_record($tablename, $timeline);
            cli_writeln('Block Position: timeline');
        }
        $cal = new stdClass();
        $cal->blockname = 'calendar_month';
        $cal->pagetypepattern = 'my-index';
        $cal->defaultregion = 'side-pre';
        $calrecord = $DB->get_record($tablename, (array)$cal);
        if ($calrecord) {
            $cal->id = $calrecord->id;
            $cal->defaultweight = 1;
            $DB->update_record($tablename, $cal);
            cli_writeln('Block Position: calendar_month');
        }
    }


}
