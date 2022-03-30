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
 * @author 2021 3iPunt <https://www.tresipunt.com/>
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
            LOGINBUTTON: '[data-action="customsso"]',
        };

        /**
         *
         */
        let REGIONS = {
            COURSE_CONTENT: '[data-region="course-content"]',
            TEACHERS_CONTENT: '[data-region="coursecard-teachers-"]',
            FOOTER_CONTENT: '[data-region="coursecard-footer-"]',
        };

        /**
         *
         */
        let SERVICES = {
            COURSECARD_EXTRA: 'theme_cbe_coursecard_extra',
            COURSECARD_TEACHERS: 'theme_cbe_coursecard_teachers'
        };

        /**
         *
         */
        let TEMPLATES = {
            COURSECARD_FOOTER: 'theme_cbe/coursecard_footer',
            COURSECARD_TEACHERS: 'theme_cbe/coursecard_teachers',
            LOADING: 'core/overlay_loading'
        };

        /**
         * @constructor
         * @param {String} region
         * @param {Number} courseId
         */
        function CourseCard(region, courseId) {

            var identifierfooter = $('[data-role="user"] [data-region="coursecard-footer-' + courseId + '"]');
            var identifierteachers = $('[data-role="user"] [data-region="coursecard-teachers-' + courseId + '"]');
            var identifiercourse = $('[data-role="user"] [data-region="course-content"][data-course-id="' + courseId + '"]');
            Templates.render(TEMPLATES.LOADING, {visible: true}).done(function(html) {
                /** FOOTER **/
                var request_footer = {
                    methodname: SERVICES.COURSECARD_EXTRA,
                    args: {
                        course_id: courseId
                    }
                };
                Ajax.call([request_footer])[0].done(function(response) {
                    var template = TEMPLATES.COURSECARD_FOOTER;
                    var view_url = response.view_url;
                    var hrefs = identifiercourse.find('a[href*="/course/"]');
                    hrefs.each(function() {
                        var href_current = $(this).attr('href');
                        if (href_current.includes('/course/')) {
                            $(this).attr('href', view_url);
                        }
                    });
                    Templates.render(template, response).done(function(html, js) {
                        identifierfooter.html(html);
                        Templates.runTemplateJS(js);
                    });
                }).fail(Notification.exception);

                /** TEACHERS **/
                var request_teachers = {
                    methodname: SERVICES.COURSECARD_TEACHERS,
                    args: {
                        course_id: courseId,
                    }
                };

                Ajax.call([request_teachers])[0].done(function(response) {
                    var template = TEMPLATES.COURSECARD_TEACHERS;
                    Templates.render(template, response).done(function(html, js) {
                        identifierteachers.html(html);
                        Templates.runTemplateJS(js);
                    });
                }).fail(Notification.exception);

            });

        }

        /** @type {jQuery} The jQuery node for the region. */
        CourseCard.prototype.node = null;

        return {
            /**
             * @param {String} region
             * @param {Number} courseId
             * @return {CourseCard}
             */
            initCourseCard: function (region, courseId) {
                return new CourseCard(region, courseId);
            }
        };
    }
);