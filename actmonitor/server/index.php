<?php
// ---- APP PATH -------------------------------------------------------------//
/*  Set the paths that miu will use to fetch classes and generate urls.
 *  This is the most basic configuration needed to get the framework to work.
 *  Everything else can be set in separate configuration files.
 *
 *  Path to miu framework:
 */
$miu_path = '/Users/guillermo/SkyDrive/Dev/Projects/git/tgk/miu-framework';

// ---- LOAD MIU -------------------------------------------------------------//
include $miu_path . '/MiuApp.php';
$miu = \miu\MiuApp::getInstance();

// ---- INCLUDES -------------------------------------------------------------//
$miu->addIncludePath(__DIR__.'/source');
$miu->addIncludePath(__DIR__.'/util');

// ---- PATHS ----------------------------------------------------------------//
$miu->setAssetsPath(__DIR__.'/assets');
$miu->setConfigPath(__DIR__.'/config');

// ---- URLS -----------------------------------------------------------------//
$miu->setURLFolder('');
$miu->setAssetsFolder('/assets');

//Run one of the following modes:

//Production:
//	return $miu->start();
//Debug:
	return $miu->startDebug();
//Test Unit:
//	return $miu->startTest();

?>
