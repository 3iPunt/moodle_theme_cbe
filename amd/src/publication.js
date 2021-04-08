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
            PUBLICATION_BUTTON: '[data-action="publicate"]',
        };

        let TEXT = {
            COMMENT_TEXT: '[data-text="comment"]',
        };

        /**
         *
         */
        let SERVICES = {
            MODULE_PUBLICATION: 'theme_cbe_publication'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} courseId
         */
        function Publication(region, courseId) {
            this.courseid = courseId;
            this.node = $(region);
            this.node.find(ACTION.PUBLICATION_BUTTON).on('click', this.onPublicationButtonClick.bind(this));
        }

        Publication.prototype.onPublicationButtonClick = function (e) {
            var comment = $(TEXT.COMMENT_TEXT).val();
            var request = {
                methodname: SERVICES.MODULE_PUBLICATION,
                args: {
                    course_id: this.courseid,
                    comment: comment
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        Publication.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} courseId
             * @return {Publication}
             */
            initPublication: function (region, courseId) {
                return new Publication(region, courseId);
            }
        };
    }
);