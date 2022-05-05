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
 * @author  2022 3iPunt <https://www.tresipunt.com/>
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
            SELECT_BUTTON: '#selsection'
        };

        /**
         *
         */
        let REGIONS = {
            MODS_CONTENT: '#list-mods'
        };

        /**
         * @param {String} region
         * @constructor
         */
        function CreateMod(region) {
            this.node = $(region);
            this.node.find(ACTION.SELECT_BUTTON).on('click', this.onSelectButtonClick.bind(this));
        }

        CreateMod.prototype.onSelectButtonClick = function (e) {
            const $mods = this.node.find(REGIONS.MODS_CONTENT);
            const $value = this.node.find(ACTION.SELECT_BUTTON).val();
            if ($value !== 'not') {
                $mods.removeClass('hidden');
            } else {
                $mods.addClass('hidden');
            }
        };

        /** @type {jQuery} The jQuery node for the region. */
        CreateMod.prototype.node = null;

        return {
            /**
             *
             * @param {String} region
             * @return {CreateMod}
             */
            initCreateMod: function (region) {
                return new CreateMod(region);
            }
        };
    }
);