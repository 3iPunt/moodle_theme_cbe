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
 * @package     theme_cbe
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

use theme_cbe\external\blockright_external;
use theme_cbe\external\board_external;
use theme_cbe\external\course_external;
use theme_cbe\external\coursecard_external;
use theme_cbe\external\module_external;
use theme_cbe\external\nextcloud_external;
use theme_cbe\external\section_external;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'theme_cbe_coursecard_extra' => [
        'classname' => coursecard_external::class,
        'methodname' => 'getcourseextra',
        'description' => 'Get data extra from course',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_coursecard_teachers' => [
        'classname' => coursecard_external::class,
        'methodname' => 'getteachers',
        'description' => 'Get teachers',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_section_create' => [
        'classname' => section_external::class,
        'methodname' => 'sectioncreate',
        'description' => 'Section Create',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_publication' => [
        'classname' => module_external::class,
        'methodname' => 'publication',
        'description' => 'Publicate Tresipuntshare module',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_publication_delete' => [
        'classname' => module_external::class,
        'methodname' => 'publication_delete',
        'description' => 'Publicate Tresipuntshare module Delete',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_publication_comment_send' => [
        'classname' => module_external::class,
        'methodname' => 'publication_comment_send',
        'description' => 'Send comment in Tresipuntshare module',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_publication_comment_delete' => [
        'classname' => module_external::class,
        'methodname' => 'publication_comment_delete',
        'description' => 'Delete comment in Tresipuntshare module',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_publication_comment_edit' => [
        'classname' => module_external::class,
        'methodname' => 'publication_comment_edit',
        'description' => 'Edit comment in Tresipuntshare module',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_create_course' => [
        'classname' => course_external::class,
        'methodname' => 'create_course',
        'description' => 'Create course',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_check_course_shortname' => [
        'classname' => course_external::class,
        'methodname' => 'check_course_shortname',
        'description' => 'Check if exist shortname',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_course_module_delete' => [
        'classname' => module_external::class,
        'methodname' => 'course_module_delete',
        'description' => 'Delete course module',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_board_anchor' => [
        'classname' => board_external::class,
        'methodname' => 'anchor',
        'description' => 'Declare Course Module as anchor in board',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_board_remove_anchor' => [
        'classname' => board_external::class,
        'methodname' => 'remove_anchor',
        'description' => 'Remove Course Module as anchor in board',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_board_visible' => [
        'classname' => board_external::class,
        'methodname' => 'visible',
        'description' => 'Declare Course Module visible in board',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_board_hidden' => [
        'classname' => board_external::class,
        'methodname' => 'hidden',
        'description' => 'Declare Course Module hidden in board',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_nextcloud_createfile' => [
        'classname' => nextcloud_external::class,
        'methodname' => 'createfile',
        'description' => 'Create file in NextCloud',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
    'theme_cbe_block_right_set_status' => [
        'classname' => blockright_external::class,
        'methodname' => 'set_status',
        'description' => 'Set user preference Block Right, expanded o contracted',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true
    ],
];
$services = [
    'theme_cbe' => [
        'functions' => [
            'theme_cbe_coursecard_extra',
            'theme_cbe_coursecard_teachers',
            'theme_cbe_section_create',
            'theme_cbe_publication',
            'theme_cbe_publication_delete',
            'theme_cbe_publication_comment_send',
            'theme_cbe_publication_comment_edit',
            'theme_cbe_publication_comment_delete',
            'theme_cbe_create_course',
            'theme_cbe_check_course_shortname',
            'theme_cbe_course_module_delete',
            'theme_cbe_board_anchor',
            'theme_cbe_board_remove_anchor',
            'theme_cbe_board_visible',
            'theme_cbe_board_hidden',
            'theme_cbe_nextcloud_createfile',
            'theme_cbe_block_right_set_status'
        ],
        'restrictedusers' => 0,
        'enabled' => 1
    ]
];