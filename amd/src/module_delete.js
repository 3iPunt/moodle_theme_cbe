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
            DELETE_BOARD_BUTTON: '.delete-module.action',
            DELETE_BUTTON: '[data-action="delete"]',
            CANCEL_BUTTON: '.btn.cancel'
        };

        /**
         *
         */
        let REGIONS = {
            MODULE: '#board-content .modulecbe'
        };

        /**
         *
         */
        let SERVICES = {
            MODULE_DELETE: 'theme_cbe_course_module_delete'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} cmid
         */
        function ModuleDelete(region, cmid) {
            this.cmid = cmid;
            this.node = $(region);
            this.module_node = $(REGIONS.MODULE + '[data-cmid="' + this.cmid + '"]');
            this.node.find(ACTION.DELETE_BUTTON).on('click', this.onDeleteButtonClick.bind(this));
            this.module_node.find(ACTION.DELETE_BOARD_BUTTON).on('click', this.onDeleteBoardButtonClick.bind(this));
            this.module_node.find(ACTION.CANCEL_BUTTON).on('click', this.onCancelButtonClick.bind(this));
        }

        ModuleDelete.prototype.onCancelButtonClick = function (e) {
            if (this.module_node.hasClass( "is_board_hidden" )) {
                this.module_node.css('opacity', '0.5');
            }
        };

        ModuleDelete.prototype.onDeleteBoardButtonClick = function (e) {
            this.module_node.css('opacity', '1');
        };

        ModuleDelete.prototype.onDeleteButtonClick = function (e) {

            $(ACTION.DELETE_BUTTON).prop( "disabled", true );

            const request = {
                methodname: SERVICES.MODULE_DELETE,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                if (response.success) {
                    location.reload();
                }
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        ModuleDelete.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} cmid
             *
             * @return {ModuleDelete}
             */
            initModuleDelete: function (region, cmid) {
                return new ModuleDelete(region, cmid);
            }
        };
    }
);