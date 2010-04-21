<?php

/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <florian.anderiasch at mayflower.de>
 * @copyright  2007-2010 Mayflower GmbH, www.mayflower.de
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
     * @param $config
     */
    function __construct (array $argv = array(), array $config = array())
    {
        $this->config = $config;
        if (!isset($config['excl'])) {
            $this->config['excl'] = array();
        }
        
        $this->argv = $argv;
        $this->argc = count($argv);
        $this->dir = dirname($argv[0]);
    }

    /**
     * Parses the console switches and acts accordingly.
     * @return array
     */
    function handleExec ()
    {
        if (!isset($this->argv[1])) {
            $ret = $this->showMissingParams();
        } elseif ($this->argv[1] == '--help' || $this->argv[1] == '-h') {
            $ret = $this->showHelp();
        } elseif ($this->argv[1] == '--version' || $this->argv[1] == '-V') {
            $ret = $this->showVersion();
        } elseif ($this->argv[1] == '--list' || $this->argv[1] == '-l') {
            $ret = $this->showTests();
        } elseif ($this->argv[1] == '--tags' || $this->argv[1] == '-t') {
            $ret = $this->showTags();
        } elseif ($this->argv[1] == '--create' || $this->argv[1] == '-c') {
            $ret = $this->doCreate();
        } elseif ($this->argv[1] == '--parse' || $this->argv[1] == '-p') {
            $ret = $this->doParse();
        } else {
            $ret = $this->showWrongParams();
        }
        
        return $ret;
    }
    
    /**
     * Prints the output and exit status code.
     * @param $ret
     */
    function printOutput (array $ret)
    {
        echo $ret['value'].PHP_EOL;
        exit($ret['code']);
    }

    /**
     * Starts the creation of XML files.
     * @return array
     */
    function doCreate ()
    {
        $pathIn  = isset($this->argv[2]) ? $this->argv[2] : '';
        $pathOut = isset($this->argv[3]) ? $this->argv[3] : '';
        
        // abort on common errors
        if (!is_executable($this->config['phc'])) {
            return array(
                'code' => 3,
                'value' => sprintf("error: phc not found at '%s'".PHP_EOL, $this->config['phc']),
            );
        }
        if ($pathIn == '' || $pathIn == '.' || !is_dir(realpath($pathIn))) {
            return array(
                'code' => 4,
                'value' => sprintf("error: input path not found at '%s'".PHP_EOL, $pathIn),
            );
        }
        if ($pathOut == '' || $pathOut == '.' || !is_dir(realpath($pathOut))) {
            return array(
                'code' => 5,
                'value' => sprintf("error: output path not found at '%s'".PHP_EOL, $pathOut),
            );
        }
        
        // exclude certain subdirs, like libraries
        if (in_array('--exclude', $this->argv)) {
            for($i = 0; $i < $this->argc; $i++) {
                if ('--exclude' == $this->argv[$i]) {
                    if (isset($this->argv[$i+1]) && strlen($this->argv[$i+1]) > 0 ) {
                        $this->config['excl'][] = realpath($this->argv[$i+1]);
                    }
                }
            }
        }
        
        // maybe skip the generation of DOT files
        if (in_array('--skip-dot', $this->argv)) {
            $this->config['skip_dot'] = true;
        }
        
        // maybe skip the generation of XML files
        if (in_array('--skip-xml', $this->argv)) {
            $this->config['skip_xml'] = true;
        }
        
        $this->config['pathInAbs']  = realpath($pathIn);
        $this->config['pathOutAbs'] = realpath($pathOut);

        $pc = new Padawan_Creation($this->config);
        $pc->start();

        // some profiling stuff
        echo $pc->pp->getProfiling();
        
        return array('code' => 0, 'value' => "creation finished");
    }

    /**
     * Starts the parsing of XML files.
     * @return array
     */
    function doParse ()
    {
        $target   = isset($this->argv[2]) ? $this->argv[2] : false;
        
        if (!is_readable($target)) {
            return array(
                'code' => 6,
                'value' => sprintf("error: %s missing or not readable\n", $target),
            );
        }
        
        $pp = new Padawan_Profiler();
        $pp->profile('def');
        
        $pparser = new Padawan_Parser($this->argv, $this->config);
        $pparser->parse($target);
        
        $pp->profile('def', true);
        echo $pp->getProfiling();
        return array('code' => 0, 'value' => "parsing finished");
    }

    /**
     * Displays info about missing parameters.
     * @return array
     */
    function showMissingParams ()
    {
        return $this->showWrongParams(2, true);
    }

    /**
     * Displays info about wrong parameters.
     * @param $code
     * @param $allMissing
     * @return array
     */
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
    
    /**
     * Displays the help text.
     * @return array
     */
    function showHelp ()
    {
        $cmd_name = basename($this->argv[0]);
        $val = '';
        $val .= "  General Options:" . PHP_EOL;
        $val .= sprintf('    Usage: %s [ -l ] [ -t ] [--version ]' . PHP_EOL, $cmd_name);
        $val .= PHP_EOL;
        $val .= "  Typical Usage:" . PHP_EOL;
        $val .= '    Step 1: create ASTs' . PHP_EOL;
        $val .= sprintf('      Usage: %s -c <source> <target> [ --skip-dot | --skip-xml | '.
                        '--exclude /abs/path --exclude ./rel/path ]' . PHP_EOL, $cmd_name);
        $val .= '    Step 2: run tests' . PHP_EOL;
        $val .= sprintf('      Usage: %s -p <path/to/dir or file> [-o /path/to/output.xml] [-v]' . PHP_EOL, $cmd_name);
        $val .= PHP_EOL . PHP_EOL;
        $val .= '  General options:' . PHP_EOL;
        $val .= "  -c, --create\t\tcreate ASTs" . PHP_EOL;
        $val .= "    --skip-dot\t\tskip creation of DOT files" . PHP_EOL;
        $val .= "    --skip-xml\t\tskip creation of XML files" . PHP_EOL;
        $val .= "    --exclude\t\t/an/absolute/path (once for each path)" . PHP_EOL;
        $val .= "    --exclude\t\t./a/relative/path" . PHP_EOL;
        $val .= "  -p, --parse\t\trun tests on ASTs" . PHP_EOL;
        $val .= "    -o\t\t\tspecify output filename" . PHP_EOL;
        $val .= "    -v\t\t\tbe a bit more verbose" . PHP_EOL;
        $val .= "  -t, --tags\t\tshow available tags" . PHP_EOL;
        $val .= "  -l, --list\t\tlist available tests" . PHP_EOL;
        $val .= "  --single a,b\t\trun single tests, separated with comma" . PHP_EOL;
        $val .= "  --tagged a,b\t\trun tests by tag, separated with comma" . PHP_EOL;
        $val .= "" . PHP_EOL;
        $val .= "  -V, --version\t\tshow version" . PHP_EOL;
        return array('code' => 0, 'value' => $val);
    }

    /**
     * Displays the version string.
     * @return array
     */
    function showVersion ()
    {
        $val = sprintf('PADAWAN %s - PHP AST-based Detection of Antipatterns,' 
                . ' Workarounds And general Nuisances' . PHP_EOL, 
                $this->config['version']);
        return array('code' => 0, 'value' => $val);
    }

    /**
     * Displays a list of all tests.
     * @return array
     */
    function showTests ()
    {
        $val = 'available patterns:' . PHP_EOL;
        foreach ($this->config['patterns'] as $k => $v) {
            $val .= sprintf("%s - %s" . PHP_EOL, str_pad($k, 30, ' '), $v['hint']);
        }
        return array('code' => 0, 'value' => $val);
    }

    /**
     * Displays a list of all tags.
     * @return array
     */
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
