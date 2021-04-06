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
        'core/str',
        'core/ajax',
        'core/templates'
    ], function ($, Str, Ajax, Templates) {
        "use strict";

        /**
         *
         */
        let ACTION = {
            CREATEBUTTON: '[data-action="createsection"]',
        };

        /**
         *
         */
        let INPUT = {
            NAME_TEXT: '[data-txt="name"]'
        };

        /**
         *
         */
        let REGIONS = {
            THEMES_CONTENT: '[data-region="themes"]'
        };

        /**
         *
         */
        let SERVICES = {
            COURSECARD_EXTRA: 'theme_cbe_section_create',
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} courseId
         */
        function Createsection(region, courseId) {

            console.log(courseId);

        }

        /** @type {jQuery} The jQuery node for the region. */
        Createsection.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} courseId
             * @return {Createsection}
             */
            initCreatesection: function (region, courseId) {
                return new Createsection(region, courseId);
            }
        };
    }
);