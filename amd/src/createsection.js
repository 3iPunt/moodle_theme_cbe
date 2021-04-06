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
            NAME_TEXT: '[data-txt="name"]',
            POSITION_RADIO_CHECKED: '.select-position:radio[name=position]:checked'
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
            SECTION_CREATE: 'theme_cbe_section_create',
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} courseId
         */
        function Createsection(region, courseId) {
            this.courseid = courseId;
            this.node = $(region);
            this.node.find(ACTION.CREATEBUTTON).on('click', this.onCreateButtonClick.bind(this));
        }

        Createsection.prototype.onCreateButtonClick = function (e) {
            var name = this.node.find(INPUT.NAME_TEXT).val();
            var position = $(INPUT.POSITION_RADIO_CHECKED).val();
            var request = {
                methodname: SERVICES.SECTION_CREATE,
                args: {
                    course_id: this.courseid,
                    name: name,
                    position: position
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

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