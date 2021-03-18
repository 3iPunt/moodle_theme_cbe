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
 * @author 2021 3iPunt <https://www.tresipunt.com/>
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
            LOGINBUTTON: '[data-action="customsso"]',
        };

        /**
         *
         */
        let REGIONS = {
            COURSE_CONTENT: 'data-region="course-content"',
        };

        /**
         * @property {string} CUSTOMSSO
         */
        let SERVICES = {
            CUSTOMSSO: 'theme_cbe_coursecard'
        };

        /**
         * @property {string} PASSWORD
         * @property {string} LOADING
         */
        let TEMPLATES = {
            /*PASSWORD: 'theme_cbe/login_form_password',*/
            LOADING: 'core/overlay_loading'
        };

        /**
         * @constructor
         * @param {String} region
         */
        function CourseCard(region) {
            alert("hola 2");
        }

        /** @type {jQuery} The jQuery node for the region. */
        CourseCard.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @return {CourseCard}
             */
            initCourseCard: function (region) {
                console.log('hola por aki');

                return new CourseCard(region);
            }
        };
    }
);