<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */

/**
 * Loop through tests and generate output
 *
 * @param string $filename
 * @param Padawan $pad
 * @param array $settings
 * @return array
 */
function traverse($filename, $pad, $settings = null) {
    if (is_null($settings)) {
        $settings = array(
            'colored' => true, 
            'verbose' => false,
            'single'  => false,
            'tagged'  => false,
            'color'   => array( 
                'green' => "\033[1;32m", 
                'red'   => "\033[1;31m", 
                'yellow'=> "\033[1;33m", 
                'cyan'  => "\033[0;36m", 
                'none'  => "\033[0m"
            )
        );
    }
    
    if ($settings['colored']) {
        $color = $settings['color'];
    } else {
        $color['red']    = '';
        $color['green']  = '';
        $color['yellow'] = '';
        $color['cyan'] = '';
        $color['none']   = '';
    }
    // We are only interested in XML files.
    $fileParts = explode('.', $filename);
    if (array_pop($fileParts) !== 'xml') {
        return null;
    }
    $out['xml'] = '';
    $out['csv'] = '';
    $out['txt'] = '';

    echo $color['cyan']."F> ".$filename.$color['none']."\n";
    $status = $pad->loadFile($filename);

    if ($status === false) {
        echo sprintf("ERR> %s empty\n", realpath($filename));
    }
    
    // execute tests
    $cfg = $pad->getConfig();
    $i = 0;
    $out_body['xml'] = '';
    $out_body['csv'] = '';
    $out_body['txt'] = '';
    $src_filename = $filename;
    
    // grab all tests
    $myTests = array_keys($cfg);
    // tagged tests only
    if (false !== $settings['tagged']) {
        $_tags = explode(',', $settings['tagged']);
        if (count($_tags) > 0) {
            $__tags = array();
            foreach($_tags as $tag) {
                foreach($cfg as $k => $v) {
                    if (in_array($tag, $v['tags'])) {
                        $__tags[] = $k;
                    }
                }
            }
            $myTests = $__tags;
        }
    }
    // single tests only
    if (false !== $settings['single']) {
        $_single = explode(',', $settings['single']);
        if (count($_single) > 0) {
            $myTests = $_single;
        }
    }
    
    // run tests
    foreach ($myTests as $testName) {
        $test = $pad->test($testName, true);
        // passed
        if ($test === false) {
            if ($settings['verbose']) {
                $te = $color['green'].'OK'.$color['none'];
                echo "  T1>".str_pad($testName, 30, " ").'- '.$te."\n";
            }
        } else {
            // default case of MATCH
            if (is_array($test[0])) {
                foreach ($test as $tk => $tv) {
                    $te = $color['yellow'].'MATCH in '.$tv[0].' (line '.$tv[1].')'.$color['none'];
                    echo "  T2>".str_pad($testName, 30, " ").'- '.$te."\n";
                    $out_body['xml'] .= sprintf('  <error line="%s" column="0" severity="%s" message="%s" pattern="%s" />%s', $tv[1], 'warning', htmlentities($pad->getHint($testName)), $testName, "\n");
                    $out_body['csv'] .= sprintf('"%s";"%s";"%s";"%s";"%s"%s', '{FILE}', $tv[1], 'warning', $pad->getHint($testName), $testName,"\n");
                    $out_body['txt'] .= sprintf("  %s\t%s\t%s (%s)\n", $tv[1], 'warning', $pad->getHint($testName), $testName);
                    #echo "____".$out_body['csv'];
                    if ($i == 0) {
                        $src_filename = $tv[0];
                        $i++;
                    }
                }
                //
            } elseif (strlen($test[0]) > 0) {
                $te = $color['yellow'].'MATCH in '.$test[0].' (line '.$test[1].')'.$color['none'];
                echo "  T3>".str_pad($testName, 30, " ").'- '.$te."\n";
                $out_body['xml'] .= sprintf('  <error line="%s" column="0" severity="%s" message="%s" pattern="%s" />%s', $test[1], 'warning', htmlentities($pad->getHint($testName)), $testName, "\n");
                $out_body['csv'] .= sprintf('"%s";"%s";"%s";"%s";"%s"%s', '{FILE}', $test[1], 'warning', $pad->getHint($testName), $testName, "\n");
                $out_body['txt'] .= sprintf("  %s\t%s\t%s (%s)\n", $test[1], 'warning', $pad->getHint($testName), $testName);
                #echo "____".$out_body['csv'];
                if ($i == 0) {
                    $src_filename = $test[0];
                    $i++;
                }
            } else {
                $te = $color['red'].'MATCH in '.$test[0].' (line '.$test[1].')'.$color['none'];
                echo "  T4>".str_pad($testName, 30, " ").'- '.$te."\n";
            }
        }
    }
    if (strlen($out_body['xml']) > 0) {
        $out['xml'] .= sprintf('<file name="%s">%s', $src_filename, "\n");
        $out['xml'] .= $out_body['xml'];
        $out['xml'] .= "</file>\n";
    }
    if (strlen($out_body['csv']) > 0) {
        #$out['csv'] = sprintf('"%s";%s%s',$src_filename, $out_body['csv'],"\n");
        $out['csv'] = str_replace('{FILE}', $src_filename, $out_body['csv']);
    }
    if (strlen($out_body['txt']) > 0) {
        $out['txt'] = sprintf("#%s\n%s",$src_filename, $out_body['txt']);
    }
    #var_dump($out['csv']);
    unset($filename);
    return $out;
}
?>
