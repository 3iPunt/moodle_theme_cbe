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
            SECTION_SELECT: '[data-action="select-section"]',
        };

        /**
         * @constructor
         * @param {String} region
         */
        function ModuleCreate(region) {
            this.node = $(region);
            this.node.find(ACTION.SECTION_SELECT).on('change', this.onSelectSectionChange.bind(this));
        }

        ModuleCreate.prototype.onSelectSectionChange = function (e) {
            const section = this.node.find(ACTION.SECTION_SELECT).val();
            var hrefs = $('[data-region="create-module"]').find('.mods a[href*="/modedit.php"]');
            hrefs.each(function() {
                var href_current = $(this).attr('href');
                var pos_sect = href_current.indexOf('&section=');
                var href_first = href_current.substr(0, pos_sect + 9);
                var href_slice = href_current.substr(pos_sect + 9);
                var pos_ret = href_slice.indexOf('&return');
                var href_last = href_slice.substr(pos_ret);
                var href_new = href_first + section + href_last;
                $(this).attr('href', href_new);
            });

        };

        /** @type {jQuery} The jQuery node for the region. */
        ModuleCreate.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @return {ModuleCreate}
             */
            initModuleCreate: function (region) {
                return new ModuleCreate(region);
            }
        };
    }
);