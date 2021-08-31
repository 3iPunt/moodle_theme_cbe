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
            CREATE_FILE_NC: '#create_file_nextcloud'
        };

        /**
         *
         */
        let ACTION = {
            CREATE_WORD_BUTTON: '[data-action="word"]',
            CREATE_EXCEL_BUTTON: '[data-action="excel"]',
            CREATE_PP_BUTTON: '[data-action="pp"]',
            CREATE_FEEDBACK_BUTTON: '[data-action="feedback"]',
            CREATE_FORM_BUTTON: '[data-action="form"]'
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
            this.node.find(ACTION.CREATE_WORD_BUTTON).on('click', this.onCreateClick.bind(this));
            this.node.find(ACTION.CREATE_EXCEL_BUTTON).on('click', this.onCreateClick.bind(this));
            this.node.find(ACTION.CREATE_PP_BUTTON).on('click', this.onCreateClick.bind(this));
            this.node.find(ACTION.CREATE_FEEDBACK_BUTTON).on('click', this.onCreateClick.bind(this));
            this.node.find(ACTION.CREATE_FORM_BUTTON).on('click', this.onCreateClick.bind(this));
        }

        Createfilenc.prototype.onCreateClick = function (e) {

            let type = $(e.target).data('action');

            const request = {
                methodname: SERVICES.NC_CREATEFILE,
                args: {
                    type: type
                }
            };

            Ajax.call([request])[0].done(function(response) {
                console.log(response);
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