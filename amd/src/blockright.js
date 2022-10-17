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
    ], function($, Str, Ajax, Templates) {
        "use strict";

        /**
         *
         */
        let ACTION = {
            CONTRACT_BUTTON: '#blockright_contract',
            EXPAND_BUTTON: '#blockright_expand',
        };

        /**
         *
         */
        let SERVICES = {
            BLOCK_RIGHT: 'theme_cbe_block_right_set_status'
        };

        /**
         * @constructor
         */
        function Blockright() {
            this.node = $('section[data-region="blocks-column"]');
            let buttonbr = '<div id="blockright_container">';
            buttonbr += '<button id="blockright_contract" type="button" class="btn-blockregion">';
            buttonbr += '<i class="fa fa-chevron-right" aria-hidden="true"></i></button>';
            buttonbr += '<button id="blockright_expand" type="button" class="btn-blockregion">';
            buttonbr += '<i class="fa fa-chevron-left" aria-hidden="true"></i></button>';
            buttonbr += '</div>';
            this.node.prepend(buttonbr);
            this.node.find(ACTION.CONTRACT_BUTTON).on('click', this.onContractButtonClick.bind(this));
            this.node.find(ACTION.EXPAND_BUTTON).on('click', this.onExpandButtonClick.bind(this));
        }

        Blockright.prototype.onExpandButtonClick = function(e) {
            console.log('expand');
            let action = 1;
            const request = {
                methodname: SERVICES.BLOCK_RIGHT,
                args: {
                    action: action
                }
            };
            Ajax.call([request])[0].done(function(response) {
                $('section[data-region="blocks-column"]').css({"width": "19rem", "padding-left": "10px", "padding-right": "10px"});
                $('#blockright_container').css({"right": "19rem"});
                $('header#page-header').addClass("expand");
                $('div#page-content').addClass("expand");
                $('#block-region-side-pre').show();
                $('#blockright_contract').show();
                $('#blockright_expand').hide();
            }).fail(Notification.exception);
        };

        Blockright.prototype.onContractButtonClick = function(e) {
            console.log('contract');
            let action = 0;
            const request = {
                methodname: SERVICES.BLOCK_RIGHT,
                args: {
                    action: action
                }
            };
            Ajax.call([request])[0].done(function(response) {
                $('section[data-region="blocks-column"]').css({"width": "0", "padding-left": "0", "padding-right": "0"});
                $('#blockright_container').css({"right": "0"});
                $('header#page-header').css({"padding-right": "0"}).removeClass("expand");
                $('div#page-content').css({"padding-right": "0"}).removeClass("expand");
                $('#block-region-side-pre').hide();
                $('#blockright_contract').hide();
                $('#blockright_expand').show();
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        Blockright.prototype.node = null;

        return {
            /**
             *
             * @return {Blockright}
             */
            initBlockright: function() {
                return new Blockright();
            }
        };
    }
);