#!/usr/bin/php
<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */

$mydir = dirname($argv[0]);
require $mydir.'/classes/base.php';
require $mydir.'/padawan.config.php';
require $mydir.'/classes/creation.php';
require $mydir.'/classes/functions.php';
require $mydir.'/classes/profiler.php';

// do not edit below
$pathIn     = '';
$pathOut    = '';

// output general info
if (!isset($argv[1])) {
    echo 'Usage: '.$argv[0].' <source> <target> [--exclude <REGEX> ] [ --skip-dot | --skip-xml ]'.PHP_EOL;
    exit(0);
} elseif ($argv[1] == '--version') {
    echo sprintf('PADAWAN %s - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances'.PHP_EOL, $padawan['version']);
    exit(0);
// temporary cleanup @TODO remove?
} elseif ($argv[1] == '--clean') {
    echo 'cleaning up...'.PHP_EOL;
    if ($pathOut != "") {
        echo "deleting XML files...".PHP_EOL;
        //TODO
        echo "deleting DOT files...".PHP_EOL;
        //TODO
    }
    exit(0);
// test mode, using PADAWAN's own tests as input
} elseif ($argv[1] == '--test') {
    echo 'operating in test mode'.PHP_EOL;
    $pathIn = './tests/';
    $pathOut = './tests_out/';
// default case, normal mode of operation
} else {
    $pathIn = $argv[1];
    $pathOut = $argv[2];
    
    // exclude certain subdirs, like libraries
    if (isset($argv[3]) && $argv[3] == '--exclude') {
        $padawan['excl'] = isset($argv[4]) ? $argv[4] : '';
    }
    
    // maybe skip the generation of DOT files
    if (in_array('--skip-dot', $argv)) {
        $padawan['skip_dot'] = true;
    }
    
    // maybe skip the generation of XML files
    if (in_array('--skip-xml', $argv)) {
        $padawan['skip_xml'] = true;
    }
}

// abort on common errors
if (!is_executable($padawan['phc'])) {
    echo sprintf("error: phc not found at '%s'".PHP_EOL, $padawan['phc']);
    exit(1);
}
if ($pathIn == '' || $pathIn == '.' || !is_dir(realpath($pathIn))) {
    echo sprintf("error: input path not found at '%s'".PHP_EOL, $pathIn);
    exit(1);
}
if ($pathOut == '' || $pathOut == '.' || !is_dir(realpath($pathOut))) {
    echo sprintf("error: output path not found at '%s'".PHP_EOL, $pathOut);
    exit(1);
}

$padawan['pathInAbs']   = realpath($pathIn);
$padawan['pathOutAbs']  = realpath($pathOut);


$pc = new Padawan_Creation($padawan);
$pc->start();

// some profiling stuff
echo $pc->pp->getProfiling();
?>
