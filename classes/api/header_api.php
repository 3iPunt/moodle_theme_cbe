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

use admin_setting_configcolourpicker;
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
            $res = $curl->get($url);
            $result = json_decode($res, true);

            if (isset($result)) {
                if (count($result)) {
                    $this->response = $this->validate($result);
                    // Set Colours
                    $this->set_colors();
                    // Set Avatar
                    $this->set_avatar();
                } else {
                    $this->response = new response(false, null,
                        new error(1001, 'Error en la petición :' . json_encode($curl->getResponse())));
                }
            } else {
                $this->response = new response(false, null,
                    new error(1002, 'Error en la petición :' . strip_tags(json_encode($res))));
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
     * Set colors.
     * @throws dml_exception
     */
    protected function set_colors() {
        $update = false;
        // Primary Color
        $primary = $this->response->data->colours->primary;
        if ($primary = $this->validateColor($primary)) {
            if (get_config('theme_cbe', 'brandcolor') !== $primary) {
                set_config('brandcolor', $primary, 'theme_cbe');
                $update = true;
            }
        }

        // Secondary Color
        $secondary = $this->response->data->colours->secondary;
        if ($secondary = $this->validateColor($secondary)) {
            if (get_config('theme_cbe', 'secondarycolor') !== $secondary) {
                set_config('secondarycolor', $secondary, 'theme_cbe');
                $update = true;
            }
        }

        // Background Color
        $background = $this->response->data->colours->background;
        if ($background = $this->validateColor($background)) {
            if (get_config('theme_cbe', 'backboardcolor') !== $background) {
                set_config('backboardcolor', $background, 'theme_cbe');
                $update = true;
            }
        }

        if ($update) {
            theme_reset_all_caches();
        }
    }

    /**
     * Set avatar.
     * @throws dml_exception
     */
    protected function set_avatar() {
        // Avatar URL
        $avatar_api_url = $this->response->data->user_avatar;
        if (get_config('theme_cbe', 'avatar_api_url') !== $avatar_api_url) {
            set_config('avatar_api_url', $avatar_api_url, 'theme_cbe');
        }
        // Profile URL
        $user_menu_items = $this->response->data->user_menu->items;
        if (count($user_menu_items) > 0) {
            foreach ($user_menu_items as $item) {
                if ($item->shortname === 'profile') {
                    $avatar_profile_url = $item->href;
                    if (get_config('theme_cbe', 'avatar_profile_url') !== $avatar_profile_url) {
                        set_config('avatar_profile_url', $avatar_profile_url, 'theme_cbe');
                    }
                }
            }
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

    /**
     * Validates the colour that was entered by the user
     *
     * @param $color
     * @return string|false
     */
    protected function validateColor($color) {

        /**
         * List of valid HTML colour names
         *
         * @var array
         */
        $colornames = array(
            'aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure',
            'beige', 'bisque', 'black', 'blanchedalmond', 'blue',
            'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse',
            'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson',
            'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray',
            'darkgrey', 'darkgreen', 'darkkhaki', 'darkmagenta',
            'darkolivegreen', 'darkorange', 'darkorchid', 'darkred',
            'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray',
            'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink',
            'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick',
            'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro',
            'ghostwhite', 'gold', 'goldenrod', 'gray', 'grey', 'green',
            'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo',
            'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen',
            'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan',
            'lightgoldenrodyellow', 'lightgray', 'lightgrey', 'lightgreen',
            'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue',
            'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow',
            'lime', 'limegreen', 'linen', 'magenta', 'maroon',
            'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple',
            'mediumseagreen', 'mediumslateblue', 'mediumspringgreen',
            'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream',
            'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive',
            'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod',
            'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip',
            'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red',
            'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown',
            'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue',
            'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan',
            'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white',
            'whitesmoke', 'yellow', 'yellowgreen'
        );

        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $color)) {
            if (strpos($color, '#')!==0) {
                $color = '#'.$color;
            }
            return $color;
        } else if (in_array(strtolower($color), $colornames)) {
            return $color;
        } else if (preg_match('/rgb\(\d{0,3}%?\, ?\d{0,3}%?, ?\d{0,3}%?\)/i', $color)) {
            return $color;
        } else if (preg_match('/rgba\(\d{0,3}%?\, ?\d{0,3}%?, ?\d{0,3}%?\, ?\d(\.\d)?\)/i', $color)) {
            return $color;
        } else if (preg_match('/hsl\(\d{0,3}\, ?\d{0,3}%, ?\d{0,3}%\)/i', $color)) {
            return $color;
        } else if (preg_match('/hsla\(\d{0,3}\, ?\d{0,3}%,\d{0,3}%\, ?\d(\.\d)?\)/i', $color)) {
            return $color;
        } else {
            return false;
        }
    }

}
