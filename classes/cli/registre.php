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

use core\hub\registration;
use curl;
use dml_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();


class registre {

    /**
     * Execute
     *
     * @param string|null $sitename
     * @param string|null $contactname
     * @param string|null $contactemail
     * @throws dml_exception
     */
    static public function execute(string $sitename = null, string $contactname = null,string $contactemail = null) {
        global $DB;

        $data = new stdClass();
        $data->name = is_null($sitename) ? '' : $sitename;
        $data->privacy = 'notdisplayed';
        $data->description = '';
        $data->language = 'ca';
        $data->street = '';
        $data->regioncode = '';
        $data->countrycode = 'ES';
        $data->geolocation = '';
        $data->contactname = is_null($contactname) ? '' : $contactname;
        $data->contactphone = '';
        $data->contactemail = is_null($contactemail) ? '' : $contactemail;
        $data->emailalert = 0;
        $data->emailalertemail = null;
        $data->commnews = 0;
        $data->commnewsemail = null;
        $data->imageurl = '';
        $data->policyagreed = 1;
        $data->contactable = 0;

        // Save the settings.
        registration::save_site_info($data);

        if (registration::is_registered()) {
            cli_writeln('Register: Site already registered!!');
        } else {

            $hub = new stdClass();
            $hub->token = get_site_identifier();
            $hub->secret = $hub->token;
            $hub->huburl = HUB_MOODLEORGHUBURL;
            $hub->hubname = 'moodle';
            $hub->confirmed = 0;
            $hub->timemodified = time();
            $hub->id = $DB->insert_record('registration_hubs', $hub);

            $params = registration::get_site_info();
            $params['token'] = $hub->token;

            $curl = new curl();

            try {
               $curl->get(
                   HUB_MOODLEORGHUBURL . '/local/hub/siteregistration.php',
                   $params);
                $response = $curl->getResponse();
                if (trim($response['HTTP/2']) === '404') {
                    cli_writeln('Register: ERROR - Not Registered!!');
                } else {
                    cli_writeln('Register: Site registered!');
                }

            } catch (\Exception $e) {
                cli_writeln('Register: ERROR - ' . $e->getMessage());
            }
        }
    }


}
