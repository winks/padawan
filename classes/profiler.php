<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
class Padawan_Profiler {
    private $count;
    private $name;

    public function __construct($name = null) {
        $this->name = $name;
        
        $this->count['xml']['num'] = 0;
        $this->count['dot']['num'] = 0;
        
        $this->count['xml']['time'] = 0;
        $this->count['dot']['time'] = 0;
        $this->count['def']['time'] = 0;
        
        $this->count['xml']['start'] = 0;
        $this->count['dot']['start'] = 0;
        $this->count['def']['start'] = 0;
    }

    public function up($mode) {
        $this->count[$mode]['num']++;
    }

    /**
     * Profiling the padawan-create run
     *
     * @param string $token
     * @param bool $stop
     */
    public function profile($token, $stop = false) {
        // some systems maybe don't know about microtime()
        if (function_exists("microtime")) {
            $s = microtime(true);
        } else {
            $s = time();
        }
        
        if ($stop) {
            $time = ($s - $this->count[$token]['start']);
            $this->count[$token]['time'] += $time;
        } else {
            $this->count[$token]['start'] = $s;
        }
    }

    /**
     * display some statistics
     * 
     * @return string $ret
     */
    public function getProfiling() {
        $ret = '';
        $pre = is_null($this->name) ? '' : '['.$this->name.'] ';
        $outputstring = 'Padawan: %sfinished creating %s %s files in %s sec'.PHP_EOL;
        if ($this->count['xml']['num'] > 0) {
            $ret .= sprintf($outputstring, $pre, $this->count['xml']['num'], 'XML' , sprintf('%01.2f', $this->count['xml']['time']));
        }
        if ($this->count['dot']['num'] > 0) {
            $ret .= sprintf($outputstring, $pre, $this->count['dot']['num'], 'DOT', sprintf('%01.2f', $this->count['dot']['time']));
        }
        $ret .= sprintf('Padawan: %stotal runtime: %s sec'.PHP_EOL, $pre, sprintf('%01.2f', $this->count['def']['time']));
        
        return $ret;
    }
}
?>
