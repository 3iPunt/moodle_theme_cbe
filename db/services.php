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

use theme_cbe\external\coursecard_external;
use theme_cbe\external\module_external;
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
        ],
        'restrictedusers' => 0,
        'enabled' => 1
    ]
];