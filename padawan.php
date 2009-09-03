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
require $mydir.'/classes/Padawan_Console.php';
require $mydir.'/classes/Padawan_Parser.php';

$pcon = new Padawan_Console($_SERVER['argv'], $padawan);
$ret = $pcon->handleExec();
echo $pcon->printOutput($ret);
