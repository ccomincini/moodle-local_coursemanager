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
 * Web service definitions
 *
 * @package    local_coursemanager
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'create_section' => [
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'create_section',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Crea una sezione in un corso identificato da idnumber',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:manageactivities,moodle/course:update',
    ],
    'update_section' => [
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'update_section',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Aggiorna una sezione usando external_id',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:update',
    ],
    'get_section_info' => [
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'get_section_info',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Ottiene informazioni su una sezione usando external_id',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/course:view',
    ],
    'add_url_resource' => [
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'add_url_resource',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Aggiunge una risorsa URL a una sezione specifica',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:manageactivities',
    ],
];

$services = [
    'Course Manager Service' => [
        'functions' => [
            'create_section',
            'update_section',
            'get_section_info',
            'add_url_resource',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'coursemanager',
    ],
];
