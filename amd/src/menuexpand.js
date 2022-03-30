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
         *
         */
        let REGIONS = {
            EXPAND_CONTENT: '[data-region="expand"]'
        };

        /**
         * @param {String} region
         * @constructor
         */
        function MenuExpand(region) {
            this.node = $(region);
            this.node.find(ACTION.EXPAND_BUTTON).on('click', this.onExpandButtonClick.bind(this));
            this.node.find(ACTION.CONTRACT_BUTTON).on('click', this.onContractButtonClick.bind(this));
        }

        MenuExpand.prototype.onExpandButtonClick = function (e) {
            this.node.find(ACTION.CONTRACT_BUTTON).show();
            $(e.currentTarget).hide();
            this.node.find(REGIONS.EXPAND_CONTENT).show();
        };

        MenuExpand.prototype.onContractButtonClick = function (e) {
            this.node.find(ACTION.EXPAND_BUTTON).show();
            $(e.currentTarget).hide();
            this.node.find(REGIONS.EXPAND_CONTENT).hide();
        };

        /** @type {jQuery} The jQuery node for the region. */
        MenuExpand.prototype.node = null;

        return {
            /**
             *
             * @param {String} region
             * @return {MenuExpand}
             */
            initMenuExpand: function (region) {
                return new MenuExpand(region);
            }
        };
    }
);