<?php

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'create_section' => array(
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'create_section',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Crea una sezione in un corso identificato da idnumber',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:manageactivities,moodle/course:update'
    ),
    'update_section' => array(
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'update_section',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Aggiorna una sezione usando external_id',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:update'
    ),
    'get_section_info' => array(
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'get_section_info',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Ottiene informazioni su una sezione usando external_id',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/course:view'
    ),
    'add_url_resource' => array(
        'classname'   => 'local_coursemanager_external',
        'methodname'  => 'add_url_resource',
        'classpath'   => 'local/coursemanager/externallib.php',
        'description' => 'Aggiunge una risorsa URL a una sezione specifica',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:manageactivities'
    )
);

$services = array(
    'Course Manager Service' => array(
        'functions' => array(
            'create_section',
            'update_section',
            'get_section_info',
            'add_url_resource'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'coursemanager'
    )
);
