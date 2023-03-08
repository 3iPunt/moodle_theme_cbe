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
 * Core Renderer
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output;

use action_menu;
use action_menu_filler;
use action_menu_link_secondary;
use coding_exception;
use context_header;
use core_text;
use core_user;
use custom_menu;
use dml_exception;
use html_writer;
use moodle_exception;
use moodle_page;
use moodle_url;
use pix_icon;
use popup_action;
use renderer_base;
use stdClass;
use theme_cbe\course;
use theme_cbe\module;
use theme_cbe\navigation\header;
use user_picture;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/lesson/locallib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Class core_renderer
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \core_renderer {

    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @staticvar int $menucount
     * @param custom_menu $menu
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function render_custom_menu(custom_menu $menu): string {

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            if (!is_siteadmin()) {
                $currentlang = '';
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname,
                    new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';

        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }


    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     * @throws \coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = get_string('loggedinasguest');
            if (!$loginpage && $withlinks) {
                $returnstr .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );
        }

        $options = array('avatarsize' => 100 );

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page, $options);

        // 3IP. AVATAR API PROFILE.
        if (!is_siteadmin()) {
            $avatar_api = get_config('theme_cbe', 'avatar_api');
            if ($avatar_api) {
                $navitems = $opts->navitems;
                $newnavitems = [];
                $item = new stdClass();
                $item->titleidentifier = fullname($USER);
                $item->title = fullname($USER);
                $item->itemtype = 'title';
                $newnavitems[] = $item;
                foreach ($navitems as $item) {
                    if ($item->titleidentifier === 'profile,moodle') {
                        $avatar_profile_url = get_config('theme_cbe', 'avatar_profile_url');
                        $url = new moodle_url($avatar_profile_url);
                        $item->url = $url;
                        $newnavitems[] = $item;
                    }
                    if ($item->titleidentifier === 'logout,moodle') {
                        $newnavitems[] = $item;
                    }
                }
                $opts->navitems = $newnavitems;
            }
        }

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext mr-1') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu_filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->set_menu_trigger(
            $returnstr
        );
        $am->set_action_label(get_string('usermenu'));
        $am->set_alignment(action_menu::TR, action_menu::BR);
        $am->set_nowrap_on_items();
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'title':
                        $am->add(html_writer::span($value->title, 'onlytext'));
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, '', null, array('class' => 'iconsmall'));
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                    $value->imgsrc,
                                    $value->title,
                                    array('class' => 'iconsmall')
                                ) . $value->title;
                        }

                        $al = new action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            array('class' => 'icon')
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
                        if ($value->titleidentifier === 'profile,moodle') {
                            $al->attributes['target'] = '_blank';
                        }
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * Wrapper for header elements.
     *
     * @return string
     * @throws moodle_exception
     */
    public function full_header(): string {
        if ($this->page->include_region_main_settings_in_header_actions() &&
            !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }
        $data = new stdClass();
        $data->settingsmenu = $this->context_header_settings_menu();
        $data->contextheader = $this->context_header();
        $data->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $data->navbar = $this->navbar();
        $data->pageheadingbutton = $this->page_heading_button();
        $data->courseheader = $this->course_header();
        $data->headeractions = $this->page->get_header_actions();
        $header_cbe = new header();
        return $this->render_from_template($header_cbe->get_template(), $header_cbe->get_data($data));
    }

    /**
     * Outputs a heading
     *
     * @param string $text
     * @param int $level
     * @param null $classes
     * @param null $id
     * @return string
     * @throws coding_exception
     */
    public function heading($text, $level = 2, $classes = null, $id = null): string {
        $level = (integer) $level;
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }

        return  html_writer::tag('h' . $level, $text, array(
            'id' => $id, 'class' => renderer_base::prepare_classes($classes)));;
    }

    /**
     * Renders the header bar.
     *
     * @param context_header $contextheader
     * @return string
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function render_context_header(context_header $contextheader): string {

        // Generate the heading first and before everything else as we might have to do an early return.
        if (!isset($contextheader->heading)) {
            $heading = $this->heading($this->page->heading, $contextheader->headinglevel);
        } else {
            $coursecbe = new course($this->page->course->id);
            $heading = $this->heading($coursecbe->get_category(), 2);
            $heading .= $this->heading($contextheader->heading, $contextheader->headinglevel);
        }

        $showheader = empty($this->page->layout_options['nocontextheader']);
        if (!$showheader) {
            // Return the heading wrapped in an sr-only element so it is only visible to screen-readers.
            return html_writer::div($heading, 'sr-only');
        }

        // All the html stuff goes here.
        $html = html_writer::start_div('page-context-header');

        // Image data.
        if (isset($contextheader->imagedata)) {
            // Header specific image.
            $html .= html_writer::div($contextheader->imagedata, 'page-header-image');
        }

        // Headings.
        $html .= html_writer::tag('div', $heading, array('class' => 'page-header-headings'));

        // Buttons.
        if (isset($contextheader->additionalbuttons)) {
            $html .= html_writer::start_div('btn-group header-button-group');
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }
                    if ($button['buttontype'] === 'message') {
                        \core_message\helper::messageuser_requirejs();
                    }
                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));
                    $image .= html_writer::span($button['title'], 'header-button-title');
                } else {
                    $image = html_writer::empty_tag('img', array(
                        'src' => $button['formattedimage'],
                        'role' => 'presentation'
                    ));
                }
                $html .= html_writer::link($button['url'], html_writer::tag('span', $image), $button['linkattributes']);
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Start output by sending the HTTP headers, and printing the HTML <head>
     * and the start of the <body>.
     *
     * To control what is printed, you should set properties on $PAGE.
     *
     * @return string HTML that you must output this, preferably immediately.
     */
    public function header() {
        global $USER, $CFG, $SESSION;

        // Give plugins an opportunity touch things before the http headers are sent
        // such as adding additional headers. The return value is ignored.
        $pluginswithfunction = get_plugins_with_function('before_http_headers', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $function();
            }
        }

        if (\core\session\manager::is_loggedinas()) {
            $this->page->add_body_class('userloggedinas');
        }

        if (isset($SESSION->justloggedin) && !empty($CFG->displayloginfailures)) {
            require_once($CFG->dirroot . '/user/lib.php');
            // Set second parameter to false as we do not want reset the counter, the same message appears on footer.
            if ($count = user_count_login_failures($USER, false)) {
                $this->page->add_body_class('loginfailures');
            }
        }

        // If the user is logged in, and we're not in initial install,
        // check to see if the user is role-switched and add the appropriate
        // CSS class to the body element.
        if (!during_initial_install() && isloggedin() && is_role_switched($this->page->course->id)) {
            $this->page->add_body_class('userswitchedrole');
        }

        // Give themes a chance to init/alter the page object.
        $this->page->theme->init_page($this->page);

        $this->page->set_state(moodle_page::STATE_PRINTING_HEADER);

        // Find the appropriate page layout file, based on $this->page->pagelayout.
        $layoutfile = $this->page->theme->layout_file($this->page->pagelayout);
        // Render the layout using the layout file.
        $rendered = $this->render_page_layout($layoutfile);

        // Slice the rendered output into header and footer.
        $cutpos = strpos($rendered, $this->unique_main_content_token);
        if ($cutpos === false) {
            $cutpos = strpos($rendered, self::MAIN_CONTENT_TOKEN);
            $token = self::MAIN_CONTENT_TOKEN;
        } else {
            $token = $this->unique_main_content_token;
        }

        if ($cutpos === false) {
            throw new coding_exception('page layout file ' . $layoutfile . ' does not contain the main content placeholder, please include "<?php echo $OUTPUT->main_content() ?>" in theme layout file.');
        }
        $header = substr($rendered, 0, $cutpos);
        $footer = substr($rendered, $cutpos + strlen($token));
        $header = $this->doctype() . $header;

        // If this theme version is below 2.4 release and this is a course view page
        if ((!isset($this->page->theme->settings->version) || $this->page->theme->settings->version < 2012101500) &&
            $this->page->pagelayout === 'course' && $this->page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
            // check if course content header/footer have not been output during render of theme layout
            $coursecontentheader = $this->course_content_header(true);
            $coursecontentfooter = $this->course_content_footer(true);
            if (!empty($coursecontentheader)) {
                // display debug message and add header and footer right above and below main content
                // Please note that course header and footer (to be displayed above and below the whole page)
                // are not displayed in this case at all.
                // Besides the content header and footer are not displayed on any other course page
                debugging('The current theme is not optimised for 2.4, the course-specific header and footer defined in course format will not be output', DEBUG_DEVELOPER);
                $header .= $coursecontentheader;
                $footer = $coursecontentfooter. $footer;
            }
        }

        send_headers($this->contenttype, $this->page->cacheable);

        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);

        return $header . $this->skip_link_target('maincontent');
    }

    /**
     * Return HTML for a pix_icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_pix_icon()} instead.
     *
     * @param string $pix short pix name
     * @param string $alt mandatory alt attribute
     * @param string $component standard compoennt name like 'moodle', 'mod_forum', etc.
     * @param array|null $attributes htm lattributes
     * @return string HTML fragment
     * @throws coding_exception
     */
    public function pix_icon($pix, $alt, $component='moodle', array $attributes = null): string {
        global $PAGE;
        $path = $PAGE->url->get_path();

        if (!is_siteadmin() &&
            (in_array($component, module::$resources) || in_array($component, module::$others)) &&
            !strpos($path, 'modedit.php')
        ) {
            global $PAGE;
            $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
            $classname = 'theme_cbe\output\module_' . $component . '_icon_component';
            $module_resource_icon_component = new $classname();
            return $output_theme_cbe->render($module_resource_icon_component);
        } else {
            $icon = new pix_icon($pix, $alt, $component, $attributes);
            return $this->render($icon);
        }

    }

    /**
     * Internal implementation of user image rendering.
     *
     * @param user_picture $userpicture
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function render_user_picture(user_picture $userpicture): string {
        global $USER;

        $user = $userpicture->user;
        $canviewfullnames = has_capability('moodle/site:viewfullnames', $this->page->context);

        if ($userpicture->alttext) {
            if (!empty($user->imagealt)) {
                $alt = $user->imagealt;
            } else {
                $alt = get_string('pictureof', '', fullname($user, $canviewfullnames));
            }
        } else {
            $alt = '';
        }

        if (empty($userpicture->size)) {
            $size = 35;
        } else if ($userpicture->size === true or $userpicture->size == 1) {
            $size = 100;
        } else {
            $size = $userpicture->size;
        }

        $class = $userpicture->class;

        if ($user->picture == 0) {
            $class .= ' defaultuserpic';
        }

        $avatar_api = get_config('theme_cbe', 'avatar_api');

        if (!is_siteadmin() && $avatar_api) {
            if ($USER->id === $user->id) {
                $src = get_config('theme_cbe', 'avatar_api_url');
            } else {
                $src = get_config('theme_cbe', 'avatar_other_users');
                $userdata = core_user::get_user($user->id);
                $src = $src . $userdata->username;
            }
        } else {
            $src = $userpicture->get_url($this->page, $this);
        }

        $attributes = array('src' => $src, 'class' => $class, 'width' => $size, 'height' => $size);
        if (!$userpicture->visibletoscreenreaders) {
            $alt = '';
        }
        $attributes['alt'] = $alt;

        if (!empty($alt)) {
            $attributes['title'] = $alt;
        }

        // get the image html output fisrt
        $output = html_writer::empty_tag('img', $attributes);

        // Show fullname together with the picture when desired.
        if ($userpicture->includefullname) {
            $output .= fullname($userpicture->user, $canviewfullnames);
        }

        // then wrap it in link if needed
        if (!$userpicture->link) {
            return $output;
        }

        if (empty($userpicture->courseid)) {
            $courseid = $this->page->course->id;
        } else {
            $courseid = $userpicture->courseid;
        }

        if ($courseid == SITEID) {
            $url = new moodle_url('/user/profile.php', array('id' => $user->id));
        } else {
            $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $courseid));
        }

        $attributes = array('href' => $url, 'class' => 'd-inline-block aabtn');
        if (!$userpicture->visibletoscreenreaders) {
            $attributes['tabindex'] = '-1';
            $attributes['aria-hidden'] = 'true';
        }

        if ($userpicture->popup) {
            $id = html_writer::random_id('userpicture');
            $attributes['id'] = $id;
            $this->add_action_handler(new popup_action('click', $url), $id);
        }

        return html_writer::tag('a', $output, $attributes);
    }
}