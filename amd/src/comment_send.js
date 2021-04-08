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
            SEND_BUTTON: '[data-action="send-comment"]'
        };

        /**
         *
         */
        let TEXT = {
            SEND_TEXT: '[data-text="comment"]'
        };

        /**
         *
         */
        let SERVICES = {
            PUBLICATION_SEND: 'theme_cbe_publication_comment_send'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} cmid
         */
        function CommentSend(region, cmid) {
            this.cmid = cmid;
            this.node = $(region);
            this.node.find(TEXT.SEND_TEXT).on('keyup', this.onTextChange.bind(this));
            this.node.find(ACTION.SEND_BUTTON).on('click', this.onSendButtonClick.bind(this));
        }

        CommentSend.prototype.onTextChange = function (e) {
            if (this.node.find(TEXT.SEND_TEXT).val().trim() !== '') {
                this.node.find(ACTION.SEND_BUTTON).show();
            } else {
                this.node.find(ACTION.SEND_BUTTON).hide();
            }
        };

        CommentSend.prototype.onSendButtonClick = function (e) {
            var comment = this.node.find(TEXT.SEND_TEXT).val();
            this.node.find(ACTION.SEND_BUTTON).hide();
            var request = {
                methodname: SERVICES.PUBLICATION_SEND,
                args: {
                    cmid: this.cmid,
                    comment: comment
                }
            };
            Ajax.call([request])[0].done(function(response) {
                location.reload();
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        CommentSend.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} cmid
             *
             * @return {CommentSend}
             */
            initCommentSend: function (region, cmid) {
                return new CommentSend(region, cmid);
            }
        };
    }
);