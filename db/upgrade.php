<?php

// ===============================================
// File: local/coursemanager/db/upgrade.php
// ===============================================
defined('MOODLE_INTERNAL') || die();

function xmldb_local_coursemanager_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    // Sempre verifica e crea la tabella se non esiste
    if ($oldversion < 2024010104) {
        // Define table local_coursemanager_sections to be created.
        $table = new xmldb_table('local_coursemanager_sections');

        // Solo se la tabella non esiste già
        if (!$dbman->table_exists($table)) {
            // Adding fields to table local_coursemanager_sections.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('external_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

            // Adding keys to table local_coursemanager_sections.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            // Adding indexes to table local_coursemanager_sections.
            $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
            $table->add_index('sectionid', XMLDB_INDEX_UNIQUE, array('sectionid'));
            $table->add_index('external_id_course', XMLDB_INDEX_UNIQUE, array('courseid', 'external_id'));

            // Create the table
            $dbman->create_table($table);

            // Log per debug
            debugging('Created table local_coursemanager_sections', DEBUG_DEVELOPER);
        }

        // Course manager savepoint reached.
        upgrade_plugin_savepoint(true, 2024010104, 'local', 'coursemanager');
    }

    return true;
}
