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
         * @constructor
         */
        function ViewAssign() {
            this.node = $('body#page-mod-assign-view');
            this.container = this.node.find('#page section[data-region="module"] .submissionstatustable');
            this.action = this.container.find('.submissionaction');
            this.table = this.container.find('.submissionsummarytable .generaltable');
            let rows = this.table.find('tr');
            let $div = $('<div>', {'class': 'submstatusresponsive'});
            rows.each(function() {
                let $row = $('<div>', {'class': 'submstatusresrow'});
                let $header = $(this).find('th.cell');
                let $value = $(this).find('td.cell');
                let header_html = '<h4 class="header">' + $header.html() + '</h4>';
                let value_html = '<p class="value">' + $value.html() + '</p>';
                $row.html(header_html + value_html);
                $div.append($row);
                console.log(this);
            });
            this.container.prepend($div);
        }

        /** @type {jQuery} The jQuery node for the region. */
        ViewAssign.prototype.node = null;

        return {
            /**
             *
             * @return {ViewAssign}
             */
            initViewAssign: function () {
                return new ViewAssign();
            }
        };
    }
);