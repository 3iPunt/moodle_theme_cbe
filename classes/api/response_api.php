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
 * Response API
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Response API
 *
 * @package    theme_cbe
 * @copyright  2021 Tresipunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_api {

    /** @var array Result API */
    protected $result;

    /** @var string Logo */
    public $logo;

    /** @var string CSS */
    public $css;

    /** @var string background_login */
    public $background_login;

    /** @var colours Colours */
    public $colours;

    /** @var user User links */
    public $user;

    /** @var user_menu User Menu links */
    public $user_menu;

    /** @var string Avatar link */
    public $user_avatar;

    /** @var string[] Apps internal */
    public $apps_internal;

    /** @var string[] Apps external */
    public $apps_external;

    /**
     * constructor.
     * @param array $result
     */
    public function __construct(array $result) {
        $this->result = $result;
    }

    /**
     * Get
     *
     * @return $this
     * @throws moodle_exception
     */
    public function get(): response_api {
        $this->set_logo();
        $this->set_colours();
        $this->set_background_login();
        $this->set_user();
        $this->set_user_menu();
        $this->set_user_avatar();
        $this->set_apps_internal();
        $this->set_apps_external();
        return $this;
    }

    /**
     * Set background_login.
     *
     * @throws moodle_exception
     */
    protected function set_background_login() {
        if (isset($this->result['background_login'])) {
            $this->background_login = $this->result['background_login'];
        } else {
            throw new moodle_exception('No se ha encontrado el fondo del login');
        }
    }

    /**
     * Set logo.
     *
     * @throws moodle_exception
     */
    protected function set_logo() {
        if (isset($this->result['logo'])) {
            $this->logo = $this->result['logo'];
        } else {
            throw new moodle_exception('No se ha encontrado logotipo');
        }
    }

    /**
     * Set colours.
     *
     * @throws moodle_exception
     */
    protected function set_colours() {
        if (isset($this->result['colours'])) {
            $this->colours = new colours($this->result['colours']);
        } else {
            throw new moodle_exception('No se han encontrado colores');
        }
    }

    /**
     * Set user.
     *
     * @throws moodle_exception
     */
    protected function set_user() {
        if (isset($this->result['user'])) {
            $this->user = new user($this->result['user']);
        } else {
            throw new moodle_exception('No se han encontrado los enlaces de usuario');
        }
    }

    /**
     * Set user.
     *
     */
    protected function set_user_menu() {
        if (isset($this->result['user_menu'])) {
            $this->user_menu = new user_menu($this->result['user_menu']);
        } else {
            $this->user_menu = new user_menu([]);
        }
    }

    /**
     * Set avatar.
     *
     */
    protected function set_user_avatar() {
        if (isset($this->result['user_avatar'])) {
            $this->user_avatar = $this->result['user_avatar'];
        } else {
            $this->user_avatar = '';
        }
    }

    /**
     * Set apps_internal.
     *
     * @throws moodle_exception
     */
    protected function set_apps_internal() {
        if (isset($this->result['apps_internal'])) {
            $apps = new apps($this->result['apps_internal'], get_config('theme_cbe', 'apssallexternals'));
            $this->apps_internal = $apps->apps;
        } else {
            throw new moodle_exception('No se han encontrado las apps internas');
        }
    }

    /**
     * Set apps_external.
     *
     * @throws moodle_exception
     */
    protected function set_apps_external() {
        if (isset($this->result['apps_external'])) {
            $apps = new apps($this->result['apps_external'], true);
            $this->apps_external = $apps->apps;
        } else {
            throw new moodle_exception('No se han encontrado las apps externas');
        }
    }


}
