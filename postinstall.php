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
 * CLI script Post Install.
 *
 *
 * @package     theme_cbe
 * @copyright   2022 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

global $CFG;

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');


require(__DIR__.'/classes/cli/registre.php');
require(__DIR__.'/classes/cli/cfg.php');
require(__DIR__.'/classes/cli/role.php');
require(__DIR__.'/classes/cli/capability.php');
require(__DIR__.'/classes/cli/functionality.php');

$usage = "Automation of the Digital Democratic environment. Site configuration and plugins, Roles, Capabilities and other functionalities.

Usage:
    # php postinstall.php

Options:
    -h --help                   Print this help.

Description.

Examples:

    # php theme/cbe/postinstall.php
        Automatization environment

";

list($options, $unrecognised) = cli_get_params([
    'help' => false
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

//\theme_cbe\cli\registre::execute();
\theme_cbe\cli\cfg::execute();
//\theme_cbe\cli\role::execute();
//\theme_cbe\cli\capability::execute();
\theme_cbe\cli\functionality::execute();


