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
        let REGIONS = {
            RESOURCES_TAB: '[data-region="resources-tab-nav"]',
            RESOURCES_CONTENT: '[data-region="resources"]',
            ACTIVITY_TAB: '[data-region="activity-tab-nav"]',
            ACTIVITY_CONTENT: '[data-region="activity"]',
            FAVOURITE_TAB: '[data-region="favourite-tab-nav"]',
            FAVOURITE_CONTENT: '[data-region="favourites"]',
            RECOMMENDED_TAB: '[data-region="recommended-tab-nav"]',
            RECOMMENDED_CONTENT: '[data-region="recommended"]'
        };

        /**
         *
         */
        let ORDER = {
            'favourite-tab-nav': 0,
            'recommended-tab-nav': 1,
            'activity-tab-nav': 2,
            'resources-tab-nav': 3,
            'default-tab-nav': 4,
        };

        /**
         * @constructor
         * @param {String} region
         */
        function ActivityChooser(region) {
            this.node = $(region);
            this.order();
            this.refresh();
            window.setInterval(() => this.refresh(), 500);
            this.node.find(REGIONS.RECOMMENDED_TAB).on('click', this.refresh.bind(this));
            this.node.find(REGIONS.RESOURCES_TAB).on('click', this.refresh.bind(this));
            this.node.find(REGIONS.ACTIVITY_TAB).on('click', this.refresh.bind(this));
            this.node.find(REGIONS.FAVOURITE_TAB).on('click', this.refresh.bind(this));
            this.node.find('[data-action="manage-module-favourite"]')
                .on('click', this.refresh.bind(this));
        }

        ActivityChooser.prototype.refresh = function (e) {
            let recommended_tab = this.node.find(REGIONS.RECOMMENDED_TAB);
            let recommended_content = this.node.find(REGIONS.RECOMMENDED_CONTENT);
            let favourite_tab = this.node.find(REGIONS.FAVOURITE_TAB);
            let favourite_content = this.node.find(REGIONS.FAVOURITE_CONTENT);
            let activity_tab = this.node.find(REGIONS.ACTIVITY_TAB);
            let resources_tab = this.node.find(REGIONS.RESOURCES_TAB);
            let all_tabs = this.node.find('.chooser-container .nav.nav-tabs .nav-item.nav-link');
            let all_contents = this.node.find('.chooser-container .tab-pane');

            if ( !activity_tab.hasClass('active') && !resources_tab.hasClass('active')) {
                if ( !favourite_tab.hasClass('d-none') ) {
                    all_tabs.removeClass('active');
                    all_contents.removeClass('active');
                    favourite_tab.addClass('active');
                    favourite_content.addClass('active');
                    recommended_tab.hide();
                } else {
                    if ( !recommended_tab.hasClass('d-none') ) {
                        recommended_tab.show();
                        all_tabs.removeClass('active');
                        all_contents.removeClass('active');
                        recommended_tab.addClass('active');
                        recommended_content.addClass('active');
                    }
                }
            } else {
                if ( !favourite_tab.hasClass('d-none') ) {
                    recommended_tab.hide();
                } else {
                    if ( !recommended_tab.hasClass('d-none') ) {
                        recommended_tab.show();
                    }
                }
            }
        };

        ActivityChooser.prototype.order = function (e) {
            $('.chooser-container .nav.nav-tabs').each(function(){
                let $this = $(this);
                $this.append($this.find('.nav-item.nav-link').get().sort(function(a, b) {
                    let region_a = $(a).data('region');
                    let region_b = $(b).data('region');
                    return ORDER[region_a] - ORDER[region_b];
                }));
            });
        };

        /** @type {jQuery} The jQuery node for the region. */
        ActivityChooser.prototype.node = null;

        return {
            /**
             * @param {String} region
             *
             * @return {ActivityChooser}
             */
            initActivityChooser: function (region) {
                return new ActivityChooser(region);
            }
        };
    }
);