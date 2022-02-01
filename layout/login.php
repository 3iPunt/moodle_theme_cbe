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

use theme_cbe\navigation\render_cbe;

defined('MOODLE_INTERNAL') || die();

/**
 * A login page layout for the CBE theme.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $OUTPUT, $SITE;

$bodyattributes = $OUTPUT->body_attributes();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true,
        ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'logo' => render_cbe::get_logo(),
    'loginbackground' => render_cbe::get_loginbackground(),
    'policies_url' => get_config('theme_cbe', 'policies'),
    'aviso_legal_url' => get_config('theme_cbe', 'aviso_legal')
];

echo $OUTPUT->render_from_template('theme_cbe/login', $templatecontext);

