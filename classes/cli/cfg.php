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

    protected $root = '';

    /**
     * Execute.
     *
     * @param string $wwwroot
     * @throws dml_exception
     */
    static public function execute(string $wwwroot) {
        // Core
        self::set(null, 'cron_enabled', true);
        self::set(null, 'guestloginbutton ', 0);
        self::set(null, 'enrol_plugins_enabled', 'manual');
        self::set(null, 'enablemobilewebservice', 0);
        self::set(null, 'enablebadges', 0);
        self::set(null, 'forcelogin', true);
        self::set('core_competency', 'enabled', 0);
        self::set('moodlecourse', 'enablecompletion', 0);
        self::set('moodlecourse', 'maxbytes', 52428800);
        self::set('moodlecourse', 'showactivitydates', 0);
        self::set(null, 'maxbytes', 52428800);
        self::set(null, 'forum_maxbytes', 512000);
        self::set('assignsubmission_file', 'maxbytes', 0);
        self::set('workshop', 'maxbytes', 0);
        // Theme
        self::set(null, 'theme', 'cbe');
        self::theme($wwwroot);
        // Big Blue Button
        self::set(null, 'bigbluebuttonbn_waitformoderator_default', true);
        self::set(null, 'bigbluebuttonbn_participant_moderator_default', '0,3,4');
        self::set(null, 'bigbluebuttonbn_recording_editable', true);
        self::set(null, 'bigbluebuttonbn_disablecam_default', false);
        self::set(null, 'bigbluebuttonbn_disablecam_editable', true);
        self::set(null, 'bigbluebuttonbn_disablemic_editable', true);
        self::set(null, 'bigbluebuttonbn_disableprivatechat_editable', true);
        self::set(null, 'bigbluebuttonbn_disablepublicchat_editable', true);
        self::set(null, 'bigbluebuttonbn_disablenote_editable', true);
        self::set(null, 'bigbluebuttonbn_hideuserlist_editable', true);
        self::set(null, 'bigbluebuttonbn_lockedlayout_editable', true);
        self::set(null, 'bigbluebuttonbn_lockonjoin_editable', true);
        self::set(null, 'bigbluebuttonbn_lockonjoinconfigurable_editable', true);
        // Others Plugins
        self::set('mod_jitsi', 'jitsi_privatesessions', 0);
        self::set('url', 'displayoptions', '0,1,3,5,6');
        self::set('url', 'display', 3);
        self::set('resource', 'displayoptions', '0,1,2,3,4,5,6');
    }

    /**
     * Theme Configuration.
     *
     * @param string $wwwroot
     * @throws dml_exception
     */
    static public function theme(string $wwwroot) {
        $index = strpos($wwwroot, 'moodle');
        if ($index !== false) {
            $default_host = substr($wwwroot,$index + 7);
            self::set('theme_cbe', 'host', $default_host);
            self::set('theme_cbe', 'logourl', 'https://api.' . $default_host . '/img/logo.png');
            self::set('theme_cbe', 'avatar_api_url', 'https://sso.' . $default_host . '/auth/realms/master/avatar-provider');
            self::set('theme_cbe', 'avatar_other_users', 'https://api.' . $default_host . '/avatar/');
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
     * @throws dml_exception
     */
    static public function set($component, $name, $value) {
        $old = get_config($component, $name);
        set_config($name, $value, $component);
        add_to_config_log($name, $old, $value, $component);
        $component = isset($component) ? $component . ' - ' : 'core - ' ;
        cli_writeln('CFG: ' . $component . $name . ': ' . $value);
    }

}
