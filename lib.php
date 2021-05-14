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
 * Theme functions.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get Main SCSS Content
 *
 * @param $theme
 * @return string
 * @throws dml_exception
 *
 */
function theme_cbe_get_main_scss_content($theme): string {
    global $CFG;
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();
    if ($filename === 'cbe.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/cbe/scss/preset/cbe.scss');
    } else if ($filename === 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/cbe/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_cbe', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/cbe/scss/preset/cbe.scss');
    }
    $cbe = file_get_contents($CFG->dirroot . '/theme/cbe/scss/cbe.scss');
    return "\n" . $scss . "\n" . $cbe;
}

/**
 * Get Extra SCSS
 *
 * @return string
 * @throws coding_exception
 */
function theme_cbe_get_extra_scss(): string {
    $theme = theme_config::load('boost');
    return theme_boost_get_extra_scss($theme);
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 * @throws dml_exception
 */
function theme_cbe_get_pre_scss(theme_config $theme): string {
    global $CFG;

    $scss = '';

    if (get_config('theme_cbe', 'flipcolor')) {
        $configurable = [
            'brandcolor' => ['primary', 'colorbutton'],
            'secondarycolor' => ['secondarycolor', 'backbutton'],
            'backboardcolor' => ['backboard'],
        ];
    } else {
        $configurable = [
            'brandcolor' => ['primary', 'backbutton'],
            'secondarycolor' => ['secondarycolor', 'colorbutton'],
            'backboardcolor' => ['backboard'],
        ];
    }


    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}
