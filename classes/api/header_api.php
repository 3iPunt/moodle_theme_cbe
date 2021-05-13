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
 * Class header_api
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\api;


use curl;
use dml_exception;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Class header_api
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header_api  {

    /** @var string Host */
    protected $host;

    /** @var response Response */
    protected $response;

    /**
     * constructor.
     *
     * @throws dml_exception
     */
    public function __construct() {
        $this->host = get_config('theme_cbe', 'host');
        if (!isset($response)) {
            $this->call();
        }
    }

    /**
     * Call API.
     *
     */
    protected function call() {
        $curl = new curl();

        $url = 'api.' . $this->host . '/json';

        $headers = array();
        $headers[] = "Content-type: application/json";
        $curl->setHeader($headers);

        try {
            $result = $curl->get($url);
            $result = json_decode($result, true);

            if (count($result)) {
                $this->response = $this->validate($result);
            } else {
                $this->response = new response(false, null,
                    new error(1001, 'Error en la petición :' . json_encode($curl->getResponse())));
            }

        } catch (\Exception $e) {
            $this->response = new response(false, null,
                new error(1000, $e->getMessage()));
        }
    }

    /**
     * Validate Json API.
     *
     * @param array $result
     * @return response
     */
    protected function validate(array $result): response {
        $response_api = new response_api($result);
        try {
            $data = $response_api->get();
            return new response(true, $data);
        } catch (moodle_exception $e) {
            return new response(false, null,
                new error(1002, $e->getMessage()));
        }
    }

    /**
     * Get Response.
     *
     * @return response
     */
    public function get_response(): response {
        return $this->response;
    }

}
