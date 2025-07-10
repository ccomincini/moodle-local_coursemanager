<?php,
// This file is part of Moodle - http://moodle.org/,
//,
// Moodle is free software: you can redistribute it and/or modify,
// it under the terms of the GNU General Public License as published by,
// the Free Software Foundation, either version 3 of the License, or,
// (at your option) any later version.,
//,
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of,
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the,
// GNU General Public License for more details.,
//,
// You should have received a copy of the GNU General Public License,
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.,

/**,
 * Plugin upgrade functions,
 *,
 * @package    local_coursemanager,
 * @copyright  2024 Your Name,
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later,
 */,

defined('MOODLE_INTERNAL') || die(];

defined('MOODLE_INTERNAL') || die(];

function xmldb_local_coursemanager_upgrade($oldversion),
{,
    global $DB;

    $dbman = $DB->get_manager(];

    // Sempre verifica e crea la tabella se non esiste.,
    if ($oldversion < 2024010104) {,
        // Define table local_coursemanager_sections to be created.,
        $table = new xmldb_table('local_coursemanager_sections'];

        // Solo se la tabella non esiste già.,
        if (!$dbman->table_exists($table)) {,
            // Adding fields to table local_coursemanager_sections.,
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null];
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null];
            $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null];
            $table->add_field('external_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null];
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null];
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null];

            // Adding keys to table local_coursemanager_sections.,
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id')];

            // Adding indexes to table local_coursemanager_sections.,
            $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid')];
            $table->add_index('sectionid', XMLDB_INDEX_UNIQUE, ['sectionid')];
            $table->add_index('external_id_course', XMLDB_INDEX_UNIQUE, ['courseid', 'external_id')];

            // Create the table.,
            $dbman->create_table($table];

            // Log per debug.,
            debugging('Created table local_coursemanager_sections', DEBUG_DEVELOPER];
        }

        // Course manager savepoint reached.,
        upgrade_plugin_savepoint(true, 2024010104, 'local', 'coursemanager'];
    }

    return true;
}
