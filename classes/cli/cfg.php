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

defined('MOODLE_INTERNAL') || die();

global $CFG;

class cfg {

    /**
     * Execute
     */
    static public function execute() {
        self::set(null, 'cron_enabled', true);
        self::set(null, 'theme', 'cbe');
        self::theme();
        purge_caches(['theme']);
        cli_writeln('Purge Cache - Theme');
    }

    /**
     * Theme Configuration.
     *
     * @throws dml_exception
     */
    static public function theme() {
        global $CFG;
        $root = $CFG->wwwroot;
        $index = strpos($root, 'moodle');
        if ($index !== false) {
            $default_host = substr($root,$index + 7);
            self::set('theme_cbe', 'host', $default_host);
            self::set('theme_cbe', 'logourl', 'https://api.' . $default_host . '/img/logo.png');
            self::set('theme_cbe', 'avatar_api_url', 'https://api.' . $default_host . '/img/logo.png');
            self::set('theme_cbe', 'avatar_other_users', 'https://sso.' . $default_host . '/avatar/');
            self::set('theme_cbe', 'avatar_profile_url', 'https://sso.' . $default_host . '/auth/realms/master/account');
            self::set('theme_cbe', 'hostnccreate', 'https://nextcloud.' . $default_host . '/apps/files');
            self::set('theme_cbe', 'aviso_legal', 'https://api.' . $default_host . '/legal');
        }
        self::set('theme_cbe', 'header_api', true);
        self::set('theme_cbe', 'avatar_api', true);
        self::set('theme_cbe', 'has_dd_link', true);
        self::set('theme_cbe', 'ddlink_url', 'https://xnet-x.net/ca/digital-democratic/');
        self::set('theme_cbe', 'importgc', true);
        self::set('theme_cbe', 'vclasses_direct', true);
        self::set('theme_cbe', 'uniquenamecourse', true);
        self::set('theme_cbe', 'apssallexternals', true);
    }

    /**
     * Set value.
     *
     * @param $component
     * @param $name
     * @param $value
     * @throws \dml_exception
     */
    static protected function set($component, $name, $value) {
        $old = get_config($component, $name);
        set_config($name, $value, $component);
        add_to_config_log($name, $old, $value, $component);
        $component = isset($component) ? $component . ' - ' : 'core - ' ;
        cli_writeln('CFG: ' . $component . $name . ': ' . $value);
    }



}
