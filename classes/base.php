<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
class Padawan {
    // the config of all rules
    private $config;
    // xml data
    private $xml;
    // a list of messages
    private $stack;
    
    //const STRIP_XMLNS = ' xmlns="http://www.phpcompiler.org/phc-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
    const STRIP_XMLNS = ' xmlns="http://www.phpcompiler.org/phc-1.0"';
    
    const TEST_COUNT = 1;
    const TEST_MATCH = 2;
    const TEST_STEP  = 4;
    
    const P_ERROR    = 1;
    const P_WARNING  = 2;
    const P_NOTICE   = 4;
    
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config) {
        $this->config = $config;
        $this->stack = array();
    }
    
    /**
     * Loads an XML file
     * 
     * @param string $filename
     * @return bool
     */
    public function loadFile($filename) {
        if (is_file($filename) && is_readable($filename)) {
            $xml = file_get_contents($filename);
            $xml = str_replace(self::STRIP_XMLNS, '', $xml);
            $this->xml = $xml;
            if ($xml === false) {
                $this->stack[] = array('cannot read file '.$filename,self::P_ERROR);
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Evaluates an XPath expression
     *
     * @param string $query
     * @param int $test
     * @param mixed $expected
     * @return bool
     */
    public function xpath($query, $test = null, $expected = null) {
        if (strlen($query) < 1) {
            return false;
        } else {
            return $this->query(array('query' => $query, 'test' => $test, 'expected' => $expected));
        }
    }
    
    /**
     * Executes a named test
     *
     * @param string $name
     * @param bool $details
     * @return bool
     */
    public function test($name, $details = false) {
        if (!isset($this->config[$name])) {
            return false;
        } else {
            return $this->query($this->config[$name], $details);
        }
    }
    
    /**
     * Executes a query
     *
     * @param array $query
     * @param bool $details
     * @return bool
     */
    private function query($query, $details = false) {
        $q_file = '/attrs/attr[@key="phc.filename"]/string';
        $q_line = '/attrs/attr[@key="phc.line_number"]/integer';
        try {
            $x = new SimpleXMLElement($this->xml);
            $x->registerxpathnamespace('AST', 'http://www.phpcompiler.org/phc-1.1');
        } catch (Exception $e) {
            $this->stack[] = array($e->__toString(), self::P_ERROR);
            return false;
        }
        if ($query['test'] == self::TEST_COUNT) {
            $result = $x->xpath($query['query']);
            if ($result !== false && count($result) == $query['expected']) {
                if ($details) {
                    $file = false;
                    $line = false;
                    // loop upwards (with ..) to find the nearest match of a line number
                    $i = 0;
                    $fixpath = '';
                    while ($file == false) {
                        $q = $query['query'].$fixpath.$q_file;
                        $file = $x->xpath($q);
                        $fixpath .= '/..';
                        $i++;
                        if ($i > 4) break;
                    }
                    $i = 0;
                    $fixpath = '';
                    while ($line == false) {
                        $q = $query['query'].$fixpath.$q_line;
                        $line = $x->xpath($q);
                        $fixpath .= '/..';
                        $i++;
                        if ($i > 4) break;
                    }
                    return array((string)$file[0][0], (string) $line[0][0]);
                }
                return true;
            }
            return false;
        } elseif ($query['test'] == self::TEST_STEP ) {
            $base = array_shift($query['query']);
            $tmp = $x->xpath($base['query']);
            // if the first step fails, we can't match
            if ($tmp === false) {
                return false;
            }
            $return = true;
            
            foreach ($query['query'] as $key => $val) {
                $res = null;
                $q = sprintf($val['query'], $tmp[0][0]);
                $res = $x->xpath($q);
            	$return = $return && empty($res);
            	
            	$ql = $q.$q_line;
                $line = $x->xpath($ql);
                if ($line === false) {
                    $ql = $base['query'].'/../..'.$q_line;
                    $line = $x->xpath($ql);
                }
                
                $qf = $q.$q_file;
                $file = $x->xpath($qf);
                if ($file === false) {
                    $qf = $base['query'].'/../..'.$q_file;
                    $file = $x->xpath($qf);
                }
                $retFile = (string)$file[0];
                $retLine = (string)$line[0];
                if (strlen($retFile) < 1){
                    $retFile = $file[0][0];
                }
                if (strlen($retLine) < 1){
                    $retLine = $line[0][0];
                }
                $result[$key] = array($retFile, $retLine);
            }
            
            if ($return != $query['expected']) {
                return false;
            } else {
                if ($details) {
                    return $result;
                } else {
                    return true;
                }
            }
        }
    }
    
    /**
     * Returns the description of a test case
     *
     * @param string $testName
     * @return string
     */
    public function getHint($testName) {
        return 'Found '.$this->config[$testName]['hint'];
    }
    
    /**
     * Returns the config
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }
}
?>
