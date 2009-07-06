<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
* @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
$tests = array();
$results = array();
$names = array();

require '../classes/base.php';
require '../padawan.config.php';
$cfg = $padawan['patterns'];

$expected = 'bool(true)
bool(false)
';

$d = dir('../tests/');
while (false !== ($entry = $d->read())) {
   if (substr($entry, -7) == '_ok.php') {
       $tests[] = substr($entry, 0, -7);
   }
}
$d->close();

$pathOutput = '../tests_out/';

foreach ($tests as $k => $v) {
    $testFile = $v;
    ob_start();
    require './template.php';
    $results[$v] = ob_get_clean();
}
ksort($results);


$ret = "<table>\n";

$i = 1;
$max = count($results);
foreach ($cfg as $key => $v) {
    $k = substr($key, 4);
    $link = '<a href="./template.php?test='.$k.'">'.$k.'</a>';
    if ($results[$k] == $expected) {
        $color = '51ff51';
        $status = 'PASSED';
    } else {
        $color = 'ff5a5a';
        $status = 'FAILED';
    }
    $hint = isset($cfg['Test'.$k]) ? htmlentities($cfg['Test'.$k]['hint']) : 'X';
    $ret .= sprintf("<tr>\n  <td>%s / %s</td>\n  <td>%s</td>\n  <td style=\"background-color:#%s\" title=\"%s\">&nbsp;%s&nbsp;</td>\n</tr>\n", 
                    str_pad($i, strlen($max),'0', STR_PAD_LEFT), $max, $link, $color, $hint, $status);
    $i++;
}
$ret .= "</table>\n";

echo $ret;
?>
