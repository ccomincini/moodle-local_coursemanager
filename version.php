<?php
// ===============================================
// File: local/coursemanager/version.php
// ===============================================
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_coursemanager';
$plugin->version = 2024011100; // v1.1.0 - Fix encoding UTF-8 messaggi errore
$plugin->requires = 2020061500; // Moodle 3.9+
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.1.0';