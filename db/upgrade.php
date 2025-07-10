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
 * Plugin upgrade functions
 *
 * @package    local_coursemanager
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Upgrade function for local_coursemanager plugin
 *
 * @param int $oldversion The old version number
 * @return bool True on success
 */
function xmldb_local_coursemanager_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024010105) {
        // Define table local_coursemanager_sections to be created.
        $table = new xmldb_table('local_coursemanager_sections');

        // Adding fields to table local_coursemanager_sections.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('external_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_coursemanager_sections.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('course_id', XMLDB_KEY_FOREIGN, ['course_id'], 'course', ['id']);
        $table->add_key('section_id', XMLDB_KEY_FOREIGN, ['section_id'], 'course_sections', ['id']);

        // Adding indexes to table local_coursemanager_sections.
        $table->add_index('external_id', XMLDB_INDEX_UNIQUE, ['external_id']);
        $table->add_index('course_external', XMLDB_INDEX_UNIQUE, ['course_id', 'external_id']);

        // Conditionally launch create table for local_coursemanager_sections.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursemanager savepoint reached.
        upgrade_plugin_savepoint(true, 2024010105, 'local', 'coursemanager');
    }

    return true;
}
