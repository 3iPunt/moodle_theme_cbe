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
 * @package   theme_cbe
 * @copyright 2021 Tresipunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_cbe\admin_settingspage_tabs;

defined('MOODLE_INTERNAL') || die();

global $ADMIN;

if ($ADMIN->fulltree) {

    $settings = new theme_boost_admin_settingspage_tabs(
        'themesettingcbe', get_string('configtitle', 'theme_cbe'));

    $page = new admin_settingpage('theme_cbe_general',
        get_string('generalsettings', 'theme_cbe'));

    $setting = (new admin_setting_configtext(
        'theme_cbe/host',
        get_string('host', 'theme_cbe'),
        get_string('host_desc', 'theme_cbe'),
        false
    ));

    $page->add($setting);

    $setting = (new admin_setting_configcheckbox(
        'theme_cbe/vclasses_direct',
        get_string('vclasses_direct', 'theme_cbe'),
        get_string('vclasses_direct_desc', 'theme_cbe'),
        false
    ));

    $page->add($setting);

    $settings->add($page);
}
