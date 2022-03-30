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
            ANCHOR_BUTTON: '[data-action="anchor"]',
            REMOVE_ANCHOR_BUTTON: '[data-action="remove-anchor"]',
            VISIBLE_BUTTON: '[data-action="visible"]',
            HIDDEN_BUTTON: '[data-action="hidden"]',
        };

        /**
         *
         */
        let SERVICES = {
            ANCHOR: 'theme_cbe_board_anchor',
            REMOVE_ANCHOR: 'theme_cbe_board_remove_anchor',
            VISIBLE: 'theme_cbe_board_visible',
            HIDDEN: 'theme_cbe_board_hidden'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} cmid
         */
        function Board(region, cmid) {
            this.cmid = cmid;
            this.node = $(region);
            this.node.find(ACTION.ANCHOR_BUTTON).on('click', this.onAnchorButtonClick.bind(this));
            this.node.find(ACTION.REMOVE_ANCHOR_BUTTON).on('click', this.onRemoveAnchorButtonClick.bind(this));
            this.node.find(ACTION.VISIBLE_BUTTON).on('click', this.onVisibleButtonClick.bind(this));
            this.node.find(ACTION.HIDDEN_BUTTON).on('click', this.onHiddenButtonClick.bind(this));
        }

        Board.prototype.onAnchorButtonClick = function (e) {
            var request = {
                methodname: SERVICES.ANCHOR,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        Board.prototype.onRemoveAnchorButtonClick = function (e) {
            var request = {
                methodname: SERVICES.REMOVE_ANCHOR,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        Board.prototype.onVisibleButtonClick = function (e) {
            var request = {
                methodname: SERVICES.VISIBLE,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        Board.prototype.onHiddenButtonClick = function (e) {
            var request = {
                methodname: SERVICES.HIDDEN,
                args: {
                    cmid: this.cmid
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        Board.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} cmid
             * @return {Board}
             */
            initBoard: function (region, cmid) {
                return new Board(region, cmid);
            }
        };
    }
);