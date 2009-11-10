<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
class Padawan_Creation {
    private $pad;
    private $debug;
    public $pp;
    
    function __construct($pad) {
        $this->pad = $pad;
        $this->pp = new Padawan_Profiler();
        $this->debug = isset($pad['debug']) ? $pad['debug'] : false;
    }
    
    /**
     * Interface to phc
     *
     * @param string $mode
     * @param array $f
     */
    private function worker($mode, $f) {
        $this->pp->profile($mode);
        $cmd = sprintf("%s --dump-%s=ast %s", $this->pad['phc'], $mode, $f['abs']);
        if ($this->debug) echo "CMD: ".$cmd.PHP_EOL;
        exec($cmd, $_txt);
        $_txt = join("\n", $_txt);
        
        if ($mode == 'xml'){
            // replace unneeded xmlns-definition
            $pat = ' xmlns="http://www.phpcompiler.org/phc-1.0"';
            $_txt = str_replace($pat, '', $_txt);
        }
    
        $f['out'] = $this->pad['pathOutAbs'].'/'.$f[$mode];
        if ($this->debug) echo "OUT: ".$f['out'].PHP_EOL;
        file_put_contents($f['out'], $_txt);          
        $this->pp->up($mode);
    
        $_output = sprintf("creating %s for '%s'...", strtoupper($mode), $f['rel']);
        echo $_output.str_repeat(' ', max(0, 70-strlen($_output))).'done'.PHP_EOL;
        $this->pp->profile($mode, true);
    }

    /**
     * Start the processing
     *
     */
    public function start() {
        $this->pp->profile('def');
        $iterator = new RecursiveDirectoryIterator($this->pad['pathInAbs']);
        $f = array();
        $excl_abs_path = isset($this->pad['excl']) ? $this->pad['excl'] : '';
        $len_excl_abs_path = strlen($excl_abs_path);
    
        // convert files to XML/DOT
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            // absolute and relative pathnames
            $f['abs'] = $file->getPathname();
            $f['rel'] = str_replace($this->pad['pathInAbs'].'/', '', $f['abs']);
            
            if ($this->debug) echo " ABS: ".$f['abs'].PHP_EOL;
            
            // ignore directories, saves a second run to recreate the structure
            if (!$file->isDir()) {
                $f['tmp'] = split('\.',$f['rel']);
                if ($this->debug) echo " REL: ".$f['rel'].PHP_EOL;

                // skip all exclude dirs
                if ($len_excl_abs_path > 0 && substr($f['abs'], 0, $len_excl_abs_path) == $excl_abs_path) {
                    continue;
                }
                        
                // skip all files with wrong extension
                $f['ext'] = strtolower(array_pop($f['tmp']));
                if (!in_array($f['ext'], $this->pad['extensions'])) {
                    continue;
                }
    
                // create output filenames
                $f['tmp2'] = $f['tmp'];
                array_push($f['tmp'], 'xml');
                array_push($f['tmp2'], 'dot');
                $f['xml'] = join('.', $f['tmp']);
                $f['dot'] = join('.', $f['tmp2']);
                
                // create directory structure where needed
                $f['dir'] = dirname($this->pad['pathOutAbs'].'/'.$f['xml']);
                if ($this->debug) echo "DIR: ".$f['dir'].PHP_EOL;
                if (!is_dir($f['dir'])) {
                   mkdir($f['dir'], 0777, true);
                }
                
                // create XML files
                if (!$this->pad['skip_xml']) {
                    $this->worker('xml', $f);
                }
                
                // create DOT files
                if (!$this->pad['skip_dot']) {
                    $this->worker('dot', $f);
                }
            }
        }
        $this->pp->profile('def', true);
    }
}
?>
