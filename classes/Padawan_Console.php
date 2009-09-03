<?php

/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
class Padawan_Console
{
    private $argv;
    private $argc;
    private $config;
    private $dir;

    /**
     * Constructor.
     * @param $argv
     * @return unknown_type
     */
    function __construct (array $argv = array(), array $config = array())
    {
        $this->config = $config;
        $this->argv = $argv;
        $this->argc = count($argv);
        $this->dir = dirname($argv[0]);
    }

    function handleExec ()
    {
        if (!isset($this->argv[1])) {
            $ret = $this->showMissingParams();
        } elseif ($this->argv[1] == '--help' || $this->argv[1] == '-h') {
            $ret = $this->showHelp();
        } elseif ($this->argv[1] == '--version' || $this->argv[1] == '-V') {
            $ret = $this->showVersion();
        } elseif ($this->argv[1] == '-l') {
            $ret = $this->showTests();
        } elseif ($this->argv[1] == '-t') {
            $ret = $this->showTags();
        } elseif ($this->argv[1] == '-c') {
            $ret = $this->doCreate();
        } elseif ($this->argv[1] == '-p') {
            $ret = $this->doParse();
        } else {
            $ret = $this->showWrongParams();
        }
        
        return $ret;
    }
    
    function showOutput (array $ret)
    {
        echo $ret['value'];
        exit($ret['code']);
    }

    function doCreate ()
    {
        $argv = $this->argv;
        $_filename = array_shift($argv);
        //strip -c
        array_shift($argv);
        $cmd = $this->dir.'/create.php '.join(' ', $argv);
        echo $cmd;
        //system($cmd);
        // @TODO
        return array('code' => 0, 'value' => "");
    }

    function doParse ()
    {
        $argv = $this->argv;
        $_filename = array_shift($argv);
        //strip -p
        array_shift($argv);
        $cmd = $this->dir.'/cli.php '.join(' ', $argv);
        echo $cmd;
        //system($cmd);
        // @TODO
        return array('code' => 0, 'value' => "");
    }

    function showMissingParams ()
    {
        return $this->showWrongParams(2, true);
    }

    function showWrongParams ($code = 1, $allMissing = false)
    {
        $ret = array();
        $ret['code'] = $code;
        $ret['value'] = '';
        if ($allMissing) {
            $ret['value'] .= sprintf('%s: missing arguments' . PHP_EOL, $this->argv[0]);
        } else { 
            $ret['value'] .= sprintf('%s: unknown arguments' . PHP_EOL, $this->argv[0]);
        }
        $ret['value'] .= sprintf('"%s --help" for further information' . PHP_EOL, 
                $this->argv[0]);
        // [--exclude <REGEX> ]
        return $ret;
    }
    
    function showHelp ()
    {
        $val = '';
        $val .= PHP_EOL;
        $val .= sprintf('Usage: %s [ -l ] [ -t ] [--version ]' . PHP_EOL, $this->argv[0]);
        $val .= '  Step 1: create ASTs' . PHP_EOL;
        $val .= sprintf('Usage: %s -c <source> <target> [ --skip-dot | --skip-xml ]' . PHP_EOL, $this->argv[0]);
        $val .= '  Step 2: run tests' . PHP_EOL;
        $val .= sprintf('Usage: %s -p <target> [-o /path/to/report] [-v]' . PHP_EOL, $this->argv[0]);
        $val .= PHP_EOL . PHP_EOL;
        $val .= '  General options:' . PHP_EOL;
        $val .= "  -c\t\tcreate ASTs" . PHP_EOL;
        $val .= "    --skip-dot\tskip creation of DOT files" . PHP_EOL;
        $val .= "    --skip-xml\tskip creation of XML files" . PHP_EOL;
        $val .= "  -p\t\trun tests on ASTs" . PHP_EOL;
        $val .= "    -o\t\tspecify output filename" . PHP_EOL;
        $val .= "    -v\t\tbe a bit more verbose" . PHP_EOL;
        $val .= "  -t\t\tshow available tags" . PHP_EOL;
        $val .= "  -l\t\tlist available tests" . PHP_EOL;
        $val .= "  --single a,b\trun single tests, separated with comma" . PHP_EOL;
        $val .= "  --tagged a,b\trun tests by tag, separated with comma" . PHP_EOL;
        $val .= "  --version\tshow version" . PHP_EOL;
        return array('code' => 0, 'value' => $val);
    }

    function showVersion ()
    {
        $val = sprintf('PADAWAN %s - PHP AST-based Detection of Antipatterns,' 
                . ' Workarounds And general Nuisances' . PHP_EOL, 
                $this->config['version']);
        return array('code' => 0, 'value' => $val);
    }

    function showTests ()
    {
        $val = 'available patterns:' . PHP_EOL;
        foreach ($this->config['patterns'] as $k => $v) {
            $val .= sprintf("%s - %s" . PHP_EOL, str_pad($k, 30, ' '), $v['hint']);
        }
        return array('code' => 0, 'value' => $val);
    }

    function showTags ()
    {
        $val = '';
        $tags = array();
        foreach ($this->config['patterns'] as $p) {
            if (is_array($p['tags'])) {
                $tags = array_merge($p['tags'], $tags);
            }
        }
        $tags = array_unique($tags);
        sort($tags);
        $val .= 'available tags:' . PHP_EOL;
        $val .= join(' ', $tags) . PHP_EOL;
        return array('code' => 0, 'value' => $val);
    }
}
?>