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

use core_php_time_limit;
use moodle_exception;
use tool_customlang_utils;
use tool_langimport\controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/customlang/locallib.php');
require_once($CFG->dirroot . '/lib/adminlib.php');

class langs {

    /**
     * Execute.
     *
     */
    static public function execute() {
        self::configlang('ca');
        get_string_manager()->reset_caches();
    }

    /**
     * Config Lang.
     *
     * @param string $lang
     */
    static public function configlang(string $lang) {
        self::install($lang);
        self::checkout($lang);
        self::import($lang);
        self::checkin($lang);
    }

    /**
     * Install Language.
     *
     * @param string $lang
     */
    static public function install(string $lang) {
        core_php_time_limit::raise();
        $controller = new controller();
        try {
            $res = $controller->install_languagepacks($lang);
            $res = $res === 0 ? 'Already Installed' : 'Installed!';
            cli_writeln('Langs: Install ' . strtoupper($lang) . ': '. $res);
            $controller->update_all_installed_languages();
        } catch (moodle_exception $e) {
            cli_writeln('Langs: Install ERROR -> ' . $e->getMessage());
        }
    }

    /**
     * Checkout Language.
     *
     * @param string $lang
     */
    static public function checkout(string $lang) {
        core_php_time_limit::raise(HOURSECS);
        raise_memory_limit(MEMORY_EXTRA);
        try {
            cli_writeln('Langs: CheckOut ' . strtoupper($lang) . ' ...');
            tool_customlang_utils::checkout($lang);
            cli_writeln('Langs: CheckOut ' . strtoupper($lang) . ' END');
        } catch (moodle_exception $e) {
            cli_writeln('Langs: CheckOut ' . strtoupper($lang) . ' ERROR -> ' . $e->getMessage());
        }
    }

    /**
     * Checkout Language.
     *
     * @param string $lang
     */
    static public function checkin(string $lang) {
        core_php_time_limit::raise(HOURSECS);
        raise_memory_limit(MEMORY_EXTRA);
        try {
            cli_writeln('Langs: CheckIn ' . strtoupper($lang) . ' ...');
            tool_customlang_utils::checkin($lang);
            cli_writeln('Langs: CheckIn ' . strtoupper($lang) . ' END');
        } catch (moodle_exception $e) {
            cli_writeln('Langs: CheckIn ' . strtoupper($lang) . ' ERROR -> ' . $e->getMessage());
        }
    }

    /**
     * Import Language.
     *
     * @param string $lang
     */
    static public function import(string $lang) {
        global $CFG;
        $routeimport = $CFG->dirroot . '/admin/tool/customlang/cli/import.php';
        cli_writeln('Langs: Import ' . strtoupper($lang));
        $source = $CFG->dirroot . '/theme/cbe/custom_langs/customlang_' . $lang . '.zip';
        if (file_exists($source)) {
            cli_writeln('Langs: *************************');
            $resultado = shell_exec("php $routeimport --lang=$lang --source=$source");
            cli_writeln($resultado);
            cli_writeln('Langs: *************************');
        } else {
            cli_writeln('Langs: ' . strtoupper($lang) . ' - File Not Found! (' . $source . ')' );
        }
    }

}
