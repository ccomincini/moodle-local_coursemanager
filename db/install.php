<?php
// ===============================================
// File: local/coursemanager/db/install.php
// ===============================================
defined('MOODLE_INTERNAL') || die();

function xmldb_local_coursemanager_install() {
    global $DB;

    // Log per debug
    debugging('Installing local_coursemanager plugin', DEBUG_DEVELOPER);
    
    return true;
}