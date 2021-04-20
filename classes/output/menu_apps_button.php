<?php
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
 * Class menu_apps_button
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_cbe\app;
use theme_cbe\course_navigation;
use theme_cbe\course_user;

defined('MOODLE_INTERNAL') || die;

/**
 * Class menu_apps_button
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu_apps_button implements renderable, templatable {

    /**
     * constructor.
     *
     */
    public function __construct() {
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->apps = $this->get_apps();
        $data->externals = $this->get_externals();
        $data->user_courses = course_user::user_get_courses();
        return $data;
    }

    /**
     * Get Apps.
     *
     * @return app[]
     */
    protected function get_apps(): array {
        $cloud = new app('cloud','fa-cloud', 'https://nextcloud.XXX/', false);
        $email = new app('email','fa-envelope-o', 'https://nextcloud.XXX/apps/mail/setup', false);
        $pads = new app('pads','fa-file-text-o', 'https://pad.XXX/', false);
        $create_file = new app('create_file','fa-file-word-o', 'https://nextcloud.XXX/apps/files', false);
        $forms = new app('forms','fa-check-square-o', 'https://nextcloud.XXX/apps/forms', false);
        $feedback = new app('feedback','fa-bar-chart', 'https://nextcloud.XXX/apps/polls', false);
        $chat = new app('chat','fa-commenting-o', 'https://nextcloud.XXX/apps/spreed', false);
        $meets_bbb = new app('meets_bbb','fa-video-camera', 'https://nextcloud.XXX/apps/bbb', false);
        $blogs = new app('blogs','fa-rss', 'https://wp.XXX/wp-login.php?saml_sso', false);
        $schedule = new app('schedule','fa-calendar', 'https://nextcloud.XXX/apps/calendar', false);
        $photos = new app('photos','fa-file-image-o', 'https://nextcloud.XXX/apps/photos', false);
        $maps = new app('maps','fa-map-marker', 'https://nextcloud.XXX/apps/map', false);
        $apps = [];
        $apps[] =  $cloud->get();
        $apps[] =  $email->get();
        $apps[] =  $pads->get();
        $apps[] =  $create_file->get();
        $apps[] =  $forms->get();
        $apps[] =  $feedback->get();
        $apps[] =  $chat->get();
        $apps[] =  $meets_bbb->get();
        $apps[] =  $blogs->get();
        $apps[] =  $schedule->get();
        $apps[] =  $photos->get();
        $apps[] =  $maps->get();
        return $apps;
    }

    /**
     * Get Externals.
     *
     * @return app[]
     */
    protected function get_externals(): array {
        $youtube = new app('youtube','fa-youtube-play', 'https://youtube.com/', true);
        $dict = new app('dict','fa-book', 'https://www.wordreference.com/', true);
        $meets_jitsi = new app('meets_jitsi','fa-video-camera', 'http://meet.jit.si/', true);
        $search = new app('search','fa-search', '#', true);
        $apps = [];
        $apps[] =  $youtube->get();
        $apps[] =  $dict->get();
        $apps[] =  $meets_jitsi->get();
        $apps[] =  $search->get();
        return $apps;
    }
}