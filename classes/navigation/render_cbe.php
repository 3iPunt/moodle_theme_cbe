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
 * Class render_cbe
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\navigation;

use dml_exception;
use stdClass;
use theme_cbe\api\header_api;

defined('MOODLE_INTERNAL') || die;

/**
 * Class render_cbe
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class render_cbe  {

    /** @var navigation Navigation */
    protected $navigation;

    /** @var string Template */
    protected $template;

    /** @var array|stdClass Data */
    protected $data;

    /** @var header_api Header API */
    protected $header_api;

    /**
     * constructor.
     * @throws dml_exception
     */
    public function __construct() {
        $this->set_navigation();
    }

    /**
     * Se
     *
     * @throws dml_exception
     */
    protected function set_navigation() {
        global $PAGE;
        if (is_siteadmin()) {
            $this->navigation = null;
        } else {
            $this->header_api = get_config('theme_cbe', 'header_api') ? new header_api() : null;
            switch ($PAGE->context->contextlevel) {
                case CONTEXT_SYSTEM:
                    $this->navigation = new system_navigation($this->header_api);
                    break;
                case CONTEXT_USER:
                    $this->navigation = new user_navigation($this->header_api);
                    break;
                case CONTEXT_COURSECAT:
                    $this->navigation = new category_navigation($this->header_api);
                    break;
                case CONTEXT_COURSE:
                    $this->navigation = new course_navigation($this->header_api);
                    break;
                case CONTEXT_MODULE:
                    $this->navigation = new course_module_navigation($this->header_api);
                    break;
                default:
                    $this->navigation = null;
            }
        }
    }

    /**
     * Get Template.
     *
     * @return string
     */
    public function get_template(): string {
        if (empty($this->template)) {
            $this->set_template();
        }
        return $this->template;
    }

    /**
     * Get Data.
     *
     * @param array|stdClass $data
     * @return array|stdClass
     */
    public function get_data($data) {
        if (empty($this->data)) {
            $this->set_data($data);
        }
        return $this->data;
    }

    /**
     * Set template.
     */
    abstract protected function set_template();

    /**
     * Get data.
     * @param array|stdClass $data
     */
    abstract protected function set_data($data);
}
