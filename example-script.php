<?php

// add composer autoload or manually
require_once 'src/Task.php';
require_once 'src/ProjectVerifier.php';
require_once 'src/Task/CheckRequiredDirectoriesTask.php';
require_once 'src/Task/CheckRequiredFilesTask.php';
require_once 'src/Task/CheckFilesPermissionsTask.php';
require_once 'src/Task/CheckDbRecordsTask.php';
require_once 'src/Task/CheckPhpExtensionsTask.php';
require_once 'src/Task/CheckInstalledCommandsTask.php';
require_once 'src/Task/CheckCustomTask.php';
require_once 'src/Task/CheckPhpIniTask.php';

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
    'Has active user?' => 'SELECT * FROM users WHERE enabled = 1 LIMIT 1',
];

// 5. PHP settings
$phpSettings = [
    
    ['option' => 'post_max_size', 'value' => '128M', 'comparator' => '>='],
    ['option' => 'upload_max_filesize', 'value' => '256M', 'comparator' => '>='],
];

// 6. PHP extensions
$phpExtensions = [
    'json',
    'gd',
    'mcrypt',
    'memcached',
    'mbstring',
    'fileinfo',
    'PDO',
    'pdo_mysql',
    'mysqli',
];

// 7. OS commands
$commands = [
    'beanstalked',
    'gulp',
    'pdfunite',
    'npm',
];

// db config manualy of from file require_once $projectPath.'/config/'.$env.'/database.php';
$dbConfig = [
    'host'      => 'localhost',
    'database'  => 'sklep',
    'username'  => 'dev',
    'password'  => 'dev',
    'charset'   => 'utf8',
];

use MrGrygson\ProjectVerifier\ProjectVerifier;
use MrGrygson\ProjectVerifier\Task\CheckRequiredDirectoriesTask;
use MrGrygson\ProjectVerifier\Task\CheckRequiredFilesTask;
use MrGrygson\ProjectVerifier\Task\CheckFilesPermissionsTask;
use MrGrygson\ProjectVerifier\Task\CheckDbRecordsTask;
use MrGrygson\ProjectVerifier\Task\CheckPhpExtensionsTask;
use MrGrygson\ProjectVerifier\Task\CheckInstalledCommandsTask;
use MrGrygson\ProjectVerifier\Task\CheckCustomTask;
use MrGrygson\ProjectVerifier\Task\CheckPhpIniTask;

$verifier = new ProjectVerifier($projectPath, APP_ENV);
$verifier->setDbConfiguration($dbConfig);
$verifier->addTask(new CheckPhpExtensionsTask($phpExtensions));
$verifier->addTask(new CheckInstalledCommandsTask($commands));
$verifier->addTask(new CheckRequiredDirectoriesTask($requiredDirectories));
$verifier->addTask(new CheckRequiredFilesTask($requiredFiles));
$verifier->addTask(new CheckFilesPermissionsTask($chmods));
$verifier->addTask(new CheckDbRecordsTask($queries));
$verifier->addTask(new CheckPhpIniTask($phpSettings));
$verifier->addTask(new CheckCustomTask('Test task', function() {
    return 2 > 1;
}));
$verifier->run();
