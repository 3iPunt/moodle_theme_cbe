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
    ], function ($) {
        "use strict";

        let BUTTONS = {
            SUBMIT_1 : '#id_submitbutton',
            SUBMIT_2 : '#id_submitbutton2',
        };

        let REGIONS = {
            ALERTS: '.form-control-feedback',
            ALERT_CBE: '#alert-cbe-modedit',
        };

        /**
         * @constructor
         */
        function Modedit() {
            let $create_file_nextcloud = $('#create_file_nextcloud');
            let $fitem_id_introattachments = $('#fitem_id_introattachments');
            let $fitem_id_files = $('#fitem_id_files');
            if ( $fitem_id_introattachments.length > 0 ) {
                $create_file_nextcloud.prependTo('#fitem_id_introattachments');
            } else if ($fitem_id_files.length > 0) {
                $create_file_nextcloud.prependTo('#fitem_id_files');
            }
            let mform = $('.mform');
            this.alertRestriction();
            mform.find(BUTTONS.SUBMIT_1).on('click', this.alertRestriction.bind(this));
            mform.find(BUTTONS.SUBMIT_2).on('click', this.alertRestriction.bind(this));
            mform.find(BUTTONS.SUBMIT_1).on('click', this.alertRestrictionName.bind(this));
            mform.find(BUTTONS.SUBMIT_2).on('click', this.alertRestrictionName.bind(this));
        }

        Modedit.prototype.alertRestriction = function (e) {
            let mform = $('.mform');
            let alerts = mform.find(REGIONS.ALERTS);
            let alert_is_visible = false;
            alerts.each(function(){
                let $this = $(this);
                let text = $.trim($this.text());
                if (text !== '') {
                    alert_is_visible = true;
                }
            });
            if (alert_is_visible) {
                let $alert = $(REGIONS.ALERT_CBE);
                $alert.removeClass('hidden');
            }
        };

        Modedit.prototype.alertRestrictionName = function (e) {
            let alert_is_visible = false;
            let mform = $('.mform');
            let $name = mform.find('#id_name');
            let $id_externalurl = mform.find('#id_externalurl');
            if ($name.val() === '' || $id_externalurl.val() === '') {
                alert_is_visible = true;
            }
            if (alert_is_visible) {
                let $alert = $(REGIONS.ALERT_CBE);
                $alert.removeClass('hidden');
            }
        };

        /** @type {jQuery} The jQuery node for the region. */
        Modedit.prototype.node = null;

        return {
            /**
             *
             * @return {Modedit}
             */
            initModedit: function () {
                return new Modedit();
            }
        };
    }
);