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
 * Installation script for the Course Manager plugin.
 *
 * @package    local_coursemanager
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure for the Course Manager plugin.
 *
 * @return bool Always returns true.
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_local_coursemanager_install()
{
    global $DB;

    // Log per debug
    debugging('Installing local_coursemanager plugin', DEBUG_DEVELOPER);

    return true;
}
