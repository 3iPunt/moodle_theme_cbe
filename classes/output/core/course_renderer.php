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
 * Course renderer.
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cbe\output\core;

use cm_info;
use coding_exception;
use core_course_renderer;
use core_text;
use html_writer;
use theme_cbe\module;

defined('MOODLE_INTERNAL') || die();


/**
 * Renderers to align Moove's course elements to what is expect
 *
 * @package     theme_cbe
 * @copyright   2021 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends core_course_renderer {
    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     * @throws coding_exception
     */
    public function course_section_cm_name_title(cm_info $mod, $displayoptions = array()): string {
        global $PAGE;
        $output = '';
        $url = $mod->url;
        if (!$mod->is_visible_on_course_page() || !$url) {
            // Nothing to be displayed to the user.
            return $output;
        }

        //Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);



        if (in_array($mod->modname, module::$resources) || in_array($mod->modname, module::$others)) {
            $activitylink = html_writer::start_tag('div', array('class' => 'cbe_icon_mod resource'));
            $output_theme_cbe = $PAGE->get_renderer('theme_cbe');
            $classname = 'theme_cbe\output\module_' . $mod->modname . '_icon_component';
            $module_resource_icon_component = new $classname($mod);
            $resourcemod = $output_theme_cbe->render($module_resource_icon_component);
            $activitylink .= $resourcemod;
        } else {
            $activitylink = html_writer::start_div('cbe_icon_mod');
            $activitylink .= html_writer::empty_tag(
                'img',
                array(
                    'src' => $mod->get_icon_url(),
                    'class' => 'iconlarge activityicon',
                    'alt' => '',
                    'role' => 'presentation',
                    'aria-hidden' => 'true'));
        }

        $activitylink .= html_writer::end_div();

        $activitylink .= html_writer::tag(
            'span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            $output .= html_writer::link(
                $url, $activitylink, array('class' => 'aalink' . $linkclasses, 'onclick' => $onclick));
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->is_visible_on_course_page()).
            $output .= html_writer::tag('div', $activitylink, array('class' => $textclasses));
        }
        return $output;
    }
}
