<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
require_once '../classes/base.php';
require_once '../padawan.config.php';
require_once '../classes/creation.php';
require_once '../classes/functions.php';
require_once '../classes/profiler.php';

if (isset($_GET['test']) && preg_match('/^(\w+)$/', $_GET['test'])) {
    $testFile = $_GET['test'];
}

if (!isset($pathOutput)) {
    $pathOutput = '../tests/patterns_tmp/';
}

$f[0] = $pathOutput.$testFile.'.xml';
$f[1] = $pathOutput.$testFile.'_ok.xml';

#var_dump($f);

$pad[0] = new Padawan($padawan['patterns']);
$pad[1] = new Padawan($padawan['patterns']);

$pad[0]->loadFile($f[0]);
$pad[1]->loadFile($f[1]);

$x[0] = $pad[0]->test('Test'.$testFile);
$x[1] = $pad[1]->test('Test'.$testFile);

#var_dump($f[0]);
#var_dump($f[1]);

var_dump($x[0]);
var_dump($x[1]);
?>
