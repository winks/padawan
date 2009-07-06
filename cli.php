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
    echo 'Usage: '.$argv[0].' </path/to/dir or file> [-o /path/to/outputfile.xml] [-v]'.PHP_EOL;
    exit(0);
} elseif ($argv[1] == '--version') {
    echo sprintf('PADAWAN %s - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances'.PHP_EOL, $padawan['version']);
    exit(0);
}

$pp = new Padawan_Profiler();
$pp->profile('def');

$default_dump['xml'] = './padawan-out.xml';
$default_dump['csv'] = './padawan-out.csv';
$default_dump['txt'] = './padawan-out.txt';
$dumpfile['xml'] = null;
$dumpfile['csv'] = null;
$dumpfile['txt'] = null;

$settings['colored'] = true;
$settings['verbose'] = false;
$settings['single']  = false;
$settings['tagged']  = false;
$settings['color']['green']  = "\033[1;32m";
$settings['color']['red']    = "\033[1;31m";
$settings['color']['yellow'] = "\033[1;33m";
$settings['color']['cyan']   = "\033[0;36m";
$settings['color']['none']   = "\033[0m";

$args = $argv;
array_shift($args);
$target   = isset($argv[1]) ? $argv[1] : false;

foreach ($args as $k => $v) {
    if ($v == '-v') {
        $settings['verbose'] = true;
    }
    if ($v == '-o') {
        if (isset($args[$k+1])) {
            $dumpfile['xml'] = $args[$k+1];
        }
    }
    if ($v == '--single') {
        if (isset($args[$k+1])) {
            $settings['single'] = $args[$k+1];
        }
    }
    if ($v == '--tagged') {
        if (isset($args[$k+1])) {
            $settings['tagged'] = $args[$k+1];
        }
    }
}

$out = array();
$out['xml'] = '';
$out['csv'] = '';
$out['txt'] = '';



if(is_readable($target)) {
    $pad = new Padawan($padawan['patterns']);
    $out['xml'] .= '<?xml version="1.0" encoding="UTF-8"?'.'>'."\n";
    $out['xml'] .= sprintf('<padawan version="%s">'."\n", $padawan['version']);
    $out['csv'] .= '"Filename";"Line";"Severity";"Message";"Pattern"'."\n";
    
    if (is_dir($target)) {
        $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target));
        
        foreach ($dir as $file) {
            // Skip hidden files.
            if (substr($file->getFilename(), 0, 1) === '.') {
                continue;
            }
            $_trav = traverse($file->getPathname(), $pad, $settings);
            if (!is_null($_trav)) {
                $out['xml'] .= $_trav['xml'];
                $out['csv'] .= $_trav['csv'];
                $out['txt'] .= $_trav['txt'];
            }
        }
    } else {
        $_trav = traverse(realpath($target), $pad, $settings);
        if (!is_null($_trav)) {
            $out['xml'] .= $_trav['xml'];
            $out['csv'] .= $_trav['csv'];
            $out['txt'] .= $_trav['txt'];
        }
    }
    
    $out['xml'] .= '</padawan>';
    
    if (isset($dumpfile['xml']) && touch($dumpfile['xml'])) {
        $outfile['xml'] = $dumpfile['xml'];
    } else {
        $outfile['xml'] = $default_dump['xml'];
    }
    $_split = split("\.", basename($outfile['xml']));
    array_pop($_split);
    $_split = join(".", $_split);
    $outfile['csv'] = sprintf("%s/%s.csv", dirname($outfile['xml']), $_split);
    $outfile['txt'] = sprintf("%s/%s.txt", dirname($outfile['xml']), $_split);
    
    //$put['xml'] = file_put_contents($outfile['xml'], $out['xml']);

    foreach ($outfile as $k => $v) {
        $put = file_put_contents($v, $out[$k]);
        // don't say success if there was no data
        $put = strlen($out[$k]) === 0 ? false : true;

        if ($settings['colored']) {
            $status = $put === false ? $settings['color']['red'].'error'.$settings['color']['none'] : $settings['color']['green'].'success'.$settings['color']['none'];
        } else {
            $status = $put === false ? 'error' : 'success';
        }
        $lastline = str_pad(sprintf("writing output to %s:", $outfile[$k]), 33, ' ');
        echo sprintf("%s- %s\n",$lastline,$status);
    }
} else {
    echo sprintf("error: %s missing or not readable\n", $target);
}
$pp->profile('def', true);
echo $pp->getProfiling();
?>
