<?php

// add composer autoload or manually
require_once 'src/Task.php';
require_once 'src/ProjectVerifier.php';
require_once 'src/Task/CheckRequiredDirectoriesTask.php';
require_once 'src/Task/CheckRequiredFilesTask.php';
require_once 'src/Task/CheckFilesPermissionsTask.php';
require_once 'src/Task/CheckDbRecordsTask.php';

// full path
$projectPath = __DIR__;
array_shift($argv);
define('APP_ENV', (array_shift($argv) ?: 'crm.app'));

// configs

// checkings
// 1. directories to check (in project path) 
$requiredDirectories = [
    'storage/logs/'.APP_ENV,
    'storage/framework/cache/'.APP_ENV,
    'storage/logs/testing/',
    'storage/framework/cache/testing/',
];

// 2. files to check
$requiredFiles = [
    'config/'.APP_ENV.'/app.php',
    'config/'.APP_ENV.'/database.php',
];

// 3. files/directories to check permissions (in project path)
$chmods = [
    'media' => 777
];

// 4. DB queries
$queries = [
    'Has active file_device?' => 'SELECT * FROM file_device LIMIT 1',
    'Has active file_volume?' => 'SELECT * FROM file_volume WHERE active = 1 LIMIT 1',
];

// db config manualy of from file require_once $projectPath.'/config/'.$env.'/database.php';
$dbConfig = [
    'connections' => [
        'mysql' => [
            'host'      => 'localhost',
            'database'  => 'fs_crm',
            'username'  => 'dev',
            'password'  => 'dev',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],
];
$dbConfig = $dbConfig['connections']['mysql'];


use MrGrygson\ProjectVerifier\ProjectVerifier;
use MrGrygson\ProjectVerifier\Task\CheckRequiredDirectoriesTask;
use MrGrygson\ProjectVerifier\Task\CheckRequiredFilesTask;
use MrGrygson\ProjectVerifier\Task\CheckFilesPermissionsTask;
use MrGrygson\ProjectVerifier\Task\CheckDbRecordsTask;

$verifier = new ProjectVerifier($projectPath, APP_ENV);
$verifier->setDbConfiguration($dbConfig);
$verifier->addTask(new CheckRequiredDirectoriesTask($requiredDirectories));
$verifier->addTask(new CheckRequiredFilesTask($requiredFiles));
$verifier->addTask(new CheckFilesPermissionsTask($chmods));
$verifier->addTask(new CheckDbRecordsTask($queries));
$verifier->run();
