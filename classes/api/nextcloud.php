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
 * Class Nextcloud
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;

use curl;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/lib/filelib.php');

/**
 * Class Nextcloud
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class nextcloud  {

    public const TIMEOUT = 30;

    /**
     * constructor.
     *
     */
    public function __construct() {

    }

    /**
     * Create File.
     *
     * @param string $type
     * @return response_nc
     */
    public function create_file(string $type): response_nc {
        // TODO. Not work!
        $curl = new curl();
        $url = 'https://nextcloud.test.digitaldemocratic.net/ocs/v2.php/apps/files/api/v1/templates/create';
        $curl->setHeader($this->get_headers());
        $params = [
            'filePath' => ''
        ];
        try {
            $req = $curl->post($url, $params, $this->get_options_curl('POST'));
            $res = $curl->getResponse();
            $data = json_decode($req, true);
            $response = new response_nc(true, '');
        } catch (\Exception $e) {
            $response = new response_nc(false, '', new error('01040', $e->getMessage()));
        }
        return $response;
    }

    /**
     * Get Headers.
     *
     * @return array
     */
    private function get_headers(): array {
        return [
            "Content-type: application/json"
        ];
    }

    /**
     * Get Options CURL.
     *
     * @param string $method
     * @return array
     */
    private function get_options_curl(string $method): array {
        return [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => self::TIMEOUT,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_0,
            'CURLOPT_CUSTOMREQUEST' => $method,
            'CURLOPT_SSL_VERIFYHOST' => 0,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
        ];
    }

}
