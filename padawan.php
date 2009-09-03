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

if (!isset($argv[1])) {
    echo $argv[0].': missing arguments'.PHP_EOL;
    echo '"'.$argv[0].' --help" for further information'.PHP_EOL;
// [--exclude <REGEX> ]
    exit(0);
} elseif ($argv[1] == '--help' || $argv[1] == '-h') {
    echo '  General options:'.PHP_EOL;
    echo 'Usage: '.$argv[0].' [ -l ] [ -t ] [--version ]'.PHP_EOL;
    echo '  Step 1: create ASTs'.PHP_EOL;
    echo 'Usage: '.$argv[0].' -c <source> <target> [ --skip-dot | --skip-xml ]'.PHP_EOL;
    echo '  Step 2: run tests'.PHP_EOL;
    echo 'Usage: '.$argv[0].' -p <target> [-o /path/to/report] [-v]'.PHP_EOL;
    echo ''.PHP_EOL;
    echo "  -c\t\tcreate ASTs".PHP_EOL;
    echo "    --skip-dot\tskip creation of DOT files".PHP_EOL;
    echo "    --skip-xml\tskip creation of XML files".PHP_EOL;
    echo "  -p\t\trun tests on ASTs".PHP_EOL;
    echo "    -o\t\tspecify output filename".PHP_EOL;
    echo "    -v\t\tbe a bit more verbose".PHP_EOL;
    echo "  -t\t\tshow available tags".PHP_EOL;
    echo "  -l\t\tlist available tests".PHP_EOL;
    echo "  --single a,b\trun single tests, separated with comma".PHP_EOL;
    echo "  --tagged a,b\trun tests by tag, separated with comma".PHP_EOL;
    echo "  --version\tshow version".PHP_EOL;
} elseif ($argv[1] == '--version') {
    echo sprintf('PADAWAN %s - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances'.PHP_EOL, $padawan['version']);
    exit(0);
} elseif ($argv[1] == '-l') {
    $pats = array();
    echo 'available patterns:'.PHP_EOL;
    foreach ($padawan['patterns'] as $k => $v) {
        echo sprintf("%s - %s".PHP_EOL, str_pad($k,30,' '),$v['hint']);
    }
    exit(0);
} elseif ($argv[1] == '-t') {
    $tags = array();
    foreach($padawan['patterns'] as $p){
        if (is_array($p['tags'])) {
            $tags = array_merge($p['tags'], $tags);
        }
    }
    $tags = array_unique($tags);
    sort($tags);
    echo 'available tags:'.PHP_EOL;
    echo join(" ",$tags).PHP_EOL;
    exit(0);
}

$_filename = array_shift($argv);

if ($argv[0] == '-c') {
    //strip -c
    array_shift($argv);
    $cmd = $mydir.'/create.php '.join(' ', $argv);
    echo $cmd;
    //system($cmd);
} else if ($argv[0] == '-p') {
    //strip -p
    array_shift($argv);
    $cmd = $mydir.'/cli.php '.join(' ', $argv);
    echo $cmd;
    //system($cmd);
} else {

}
?>
