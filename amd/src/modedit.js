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
 * @package theme_cbe
 * @author  2021 3iPunt <https://www.tresipunt.com/>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-disable no-unused-vars */
/* eslint-disable no-console */

define([
        'jquery',
    ], function ($) {
        "use strict";

        /**
         * @constructor
         */
        function Modedit() {
            $('#create_file_nextcloud').prependTo('#fitem_id_introattachments');
        }

        /** @type {jQuery} The jQuery node for the region. */
        Modedit.prototype.node = null;

        return {
            /**
             *
             * @return {Modedit}
             */
            initModedit: function () {
                return new Modedit();
            }
        };
    }
);