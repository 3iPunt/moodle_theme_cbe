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
            EXPAND_BUTTON: '[data-action="expand"]',
            CONTRACT_BUTTON: '[data-action="contract"]',
        };

        /**
         * @constructor
         * @param {String} region
         */
        function ViewMedia(region) {
            this.node = $(region);
            this.node.find(ACTION.EXPAND_BUTTON).on('click', this.onExpandButtonClick.bind(this));
            this.node.find(ACTION.CONTRACT_BUTTON).on('click', this.onContractButtonClick.bind(this));
        }

        ViewMedia.prototype.onExpandButtonClick = function (e) {
            const cmid = $(e.currentTarget).data('cmid');
            const media = this.node.find('.media-content[data-media="' + cmid + '"]');
            const button_contract = this.node.find('.contract-media[data-cmid="' + cmid + '"]');
            $(e.currentTarget).hide();
            $(button_contract).show();
            $(media).show();
        };

        ViewMedia.prototype.onContractButtonClick = function (e) {
            const cmid = $(e.currentTarget).data('cmid');
            const media = this.node.find('.media-content[data-media="' + cmid + '"]');
            const button_expand = this.node.find('.expand-media[data-cmid="' + cmid + '"]');
            $(e.currentTarget).hide();
            $(button_expand).show();
            $(media).hide();
        };

        /** @type {jQuery} The jQuery node for the region. */
        ViewMedia.prototype.node = null;

        return {
            /**
             * @param {String} region
             *
             * @return {ViewMedia}
             */
            initViewMedia: function (region) {
                return new ViewMedia(region);
            }
        };
    }
);