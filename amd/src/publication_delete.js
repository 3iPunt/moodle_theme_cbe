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
 *
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
            DELETE_BUTTON: '[data-action="delete"]'
        };

        /**
         *
         */
        let SERVICES = {
            PUBLICATION_DELETE: 'theme_cbe_publication_delete'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} cmid
         */
        function PublicationDelete(region, cmid) {
            this.cmid = cmid;
            this.node = $(region + '[data-cmid="' + cmid +'"]');
            this.node.find(ACTION.DELETE_BUTTON).on('click', this.onDeleteButtonClick.bind(this));
        }

        PublicationDelete.prototype.onDeleteButtonClick = function (e) {

            $(ACTION.DELETE_BUTTON).prop( "disabled", true );

            const request = {
                methodname: SERVICES.PUBLICATION_DELETE,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        PublicationDelete.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} cmid
             *
             * @return {PublicationDelete}
             */
            initPublicationDelete: function (region, cmid) {
                return new PublicationDelete(region, cmid);
            }
        };
    }
);