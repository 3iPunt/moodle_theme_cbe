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

        let ACTION = {
            CREATE_BUTTON: '[data-action="create"]',
        };

        let FORM = {
            ALL_INPUTS: '.validation-input',
            FULLNAME_INPUT: '#fullname',
            SHORTNAME_INPUT: '#shortname',
            CATEGORY_SELECT: '#category',
            VISIBLE_CHECK: '#visible',
        };

        let MSG = {
            SHORTNAME: '[data-msg="msg-shortname"]',
        };

        let VERYFY_CHECK = {
            FULLNAME_OK: '#verify-fullname .fa-check-circle-o',
            FULLNAME_KO: '#verify-fullname .fa-times-circle-o',
            SHORTNAME_OK: '#verify-shortname .fa-check-circle-o',
            SHORTNAME_KO: '#verify-shortname .fa-times-circle-o',
            CATEGORY_OK: '#verify-category .fa-check-circle-o',
            CATEGORY_KO: '#verify-category .fa-times-circle-o'
        };

        let SERVICES = {
            CREATE_COURSE: 'theme_cbe_create_course',
            CHECK_COURSE_SHORTNAME: 'theme_cbe_check_course_shortname',
        };

        let VALIDATION = {
            'fullname': false,
            'shortname': false,
            'category': false
        };

        /**
         * @constructor
         * @param {String} region
         * @param {boolean} has_uniquename
         */
        function CreateCourse(region, has_uniquename) {
            this.node = $(region);
            this.has_uniquename = has_uniquename;
            this.fullname = FORM.FULLNAME_INPUT;
            this.shortname = FORM.SHORTNAME_INPUT;
            this.category = FORM.CATEGORY_SELECT;
            this.all_inputs = FORM.ALL_INPUTS;
            if (!this.has_uniquename) {
                this.node.find(this.fullname).on('change', this.onFullnameChange.bind(this));
            } else {
                VALIDATION.fullname = true;
            }
            this.node.find(this.shortname).on('keyup', this.onShortnameChange.bind(this));
            this.node.find(this.category).on('change', this.onCategoryChange.bind(this));
            this.node.find(this.all_inputs).on('change', this.onAllInputsChange.bind(this));
            this.node.find(ACTION.CREATE_BUTTON).on('click', this.onCreateCourseButtonClick.bind(this));
        }

        CreateCourse.prototype.onAllInputsChange = function (e) {
            if (VALIDATION.fullname && VALIDATION.shortname && VALIDATION.category) {
                $(ACTION.CREATE_BUTTON).prop( "disabled", false );
            } else {
                $(ACTION.CREATE_BUTTON).prop( "disabled", true );
            }
        };

        CreateCourse.prototype.onCategoryChange = function (e) {
            if (this.node.find(this.category).val().trim() > 0) {
                VALIDATION.category = true;
                $(VERYFY_CHECK.CATEGORY_OK).show();
                $(VERYFY_CHECK.CATEGORY_KO).hide();
            } else {
                VALIDATION.category = false;
                $(VERYFY_CHECK.CATEGORY_OK).hide();
                $(VERYFY_CHECK.CATEGORY_KO).show();
            }
        };

        CreateCourse.prototype.onFullnameChange = function (e) {
            if (this.node.find(this.fullname).val().trim() !== '') {
                VALIDATION.fullname = true;
                $(VERYFY_CHECK.FULLNAME_OK).show();
                $(VERYFY_CHECK.FULLNAME_KO).hide();
            } else {
                VALIDATION.fullname = false;
                $(VERYFY_CHECK.FULLNAME_KO).show();
                $(VERYFY_CHECK.FULLNAME_OK).hide();
            }
        };

        CreateCourse.prototype.onShortnameChange = function (e) {
            const shortname = $(FORM.SHORTNAME_INPUT).val().trim();
            if (this.node.find(this.shortname).val().length > 3) {
                const request = {
                    methodname: SERVICES.CHECK_COURSE_SHORTNAME,
                    args: {
                        shortname: shortname
                    }
                };
                Ajax.call([request])[0].done(function(response) {
                    if (response.exist) {
                        VALIDATION.shortname = false;
                        $(MSG.SHORTNAME).text(response.msg);
                        $(VERYFY_CHECK.SHORTNAME_KO).show();
                        $(VERYFY_CHECK.SHORTNAME_OK).hide();
                        if (VALIDATION.fullname && VALIDATION.shortname && VALIDATION.category) {
                            $(ACTION.CREATE_BUTTON).prop( "disabled", false );
                        } else {
                            $(ACTION.CREATE_BUTTON).prop( "disabled", true );
                        }
                    } else {
                        $(VERYFY_CHECK.SHORTNAME_OK).show();
                        $(VERYFY_CHECK.SHORTNAME_KO).hide();
                        $(MSG.SHORTNAME).text('');
                        VALIDATION.shortname = true;
                        if (VALIDATION.fullname && VALIDATION.shortname && VALIDATION.category) {
                            $(ACTION.CREATE_BUTTON).prop( "disabled", false );
                        } else {
                            $(ACTION.CREATE_BUTTON).prop( "disabled", true );
                        }
                    }
                }).fail(Notification.exception);
            } else {
                VALIDATION.shortname = false;
                $(VERYFY_CHECK.SHORTNAME_KO).show();
                $(VERYFY_CHECK.SHORTNAME_OK).hide();
            }
        };

        CreateCourse.prototype.onCreateCourseButtonClick = function (e) {
            $(ACTION.CREATE_BUTTON).prop( "disabled", true );
            let fullname = '';
            if (!this.has_uniquename) {
                fullname = $(FORM.FULLNAME_INPUT).val().trim();
            } else {
                fullname = $(FORM.SHORTNAME_INPUT).val().trim();
            }
            const shortname = $(FORM.SHORTNAME_INPUT).val().trim();
            const category = $(FORM.CATEGORY_SELECT).val();
            const visible = $(FORM.VISIBLE_CHECK).is(":checked") ? 1 : 0;
            const request = {
                methodname: SERVICES.CREATE_COURSE,
                args: {
                    fullname: fullname,
                    shortname: shortname,
                    category: category,
                    visible: visible
                }
            };
            Ajax.call([request])[0].done(function(response) {
                if (response.success) {
                    location.href = response.redirect;
                } else {
                    alert(response.error);
                }
            }).fail(Notification.exception);
        };

        /** @type {jQuery} The jQuery node for the region. */
        CreateCourse.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {boolean} has_uniquename
             * @return {CreateCourse}
             */
            initCreateCourse: function (region, has_uniquename) {
                return new CreateCourse(region, has_uniquename);
            }
        };
    }
);