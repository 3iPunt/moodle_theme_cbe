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
        'core/ajax',
    ], function ($,Ajax) {
        "use strict";

        /**
         *
         */
        let REGION = {
            CREATE_FILE_NC: '#create_file_nextcloud',
            BODY_NC: '#create_file_nextcloud_body',
            IFRAME_NC: '#create_file_nextcloud_iframe',
            ERROR_NC: '#nextcloud_error'
        };

        /**
         *
         */
        let ACTION = {
            CREATE_BUTTON: '[data-action="create_file_nextcloud"]',
            CONTRACT_BUTTON: '[data-action="create_file_nextcloud_contract"]'
        };

        /**
         *
         */
        let SERVICES = {
            NC_CREATEFILE: 'theme_cbe_nextcloud_createfile',
        };

        /**
         * @constructor
         */
        function Createfilenc() {
            this.node = $(REGION.CREATE_FILE_NC);
            this.node.find(ACTION.CREATE_BUTTON).on('click', this.onCreateClick.bind(this));
            this.node.find(ACTION.CONTRACT_BUTTON).on('click', this.onContractClick.bind(this));
        }

        Createfilenc.prototype.onContractClick = function (e) {

            this.node.find(REGION.BODY_NC).hide();
            this.node.find(ACTION.CONTRACT_BUTTON).hide();
            this.node.find(ACTION.CREATE_BUTTON).show();
            this.node.find(REGION.IFRAME_NC).hide();

        };

        Createfilenc.prototype.onCreateClick = function (e) {

            this.node.find(REGION.BODY_NC).show();
            this.node.find(ACTION.CONTRACT_BUTTON).show();
            this.node.find(ACTION.CREATE_BUTTON).hide();
            this.node.find(REGION.ERROR_NC).hide();

            let node = this.node;


            const request = {
                methodname: SERVICES.NC_CREATEFILE,
                args: {}
            };

            Ajax.call([request])[0].done(function(response) {
                if (response.success) {
                    node.find(REGION.IFRAME_NC).show();
                    node.find(REGION.IFRAME_NC).attr('src', response.link);
                } else {
                    node.find(REGION.IFRAME_NC).hide();
                    node.find(REGION.ERROR_NC).show();
                    node.find(REGION.ERROR_NC).text(response.error);
                }
            }).fail(Notification.exception);

        };

        /** @type {jQuery} The jQuery node for the region. */
        Createfilenc.prototype.node = null;

        return {
            /**
             *
             * @return {Createfilenc}
             */
            initCreatefilenc: function () {
                return new Createfilenc();
            }
        };
    }
);