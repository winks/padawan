<?php
require_once '../classes/base.php';
require_once '../padawan.config.php';
/**
 * Padawan test case for all patterns.
 */
class Padawan_PatternTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var pathIn
     * @var pathOut
     */
    private $pathIn;
    private $pathOut;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->pathIn = './patterns/';
        $this->pathIn = './patterns_tmp/';
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {
    }
    /**
     * Tests Padawan->loadFile()
     */
    public function testPatterns ()
    {
        $my_tests = array();
        foreach (new DirectoryIterator($this->pathIn) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $tf = split(".", $fileInfo->getFilename());
            array_pop($tf);
            $testFile = join(".", $tf);
            $my_tests[] = $testFile;
            
            $f[0] = $this->pathOut.$testFile.'.xml';
            $f[1] = $this->pathOut.$testFile.'_ok.xml';

            $pad[0] = new Padawan($GLOBALS['padawan']['patterns']);
            $pad[1] = new Padawan($GLOBALS['padawan']['patterns']);

            $pad[0]->loadFile($f[0]);
            $pad[1]->loadFile($f[1]);

            $x[0] = $pad[0]->test('Test'.$testFile);
            $x[1] = $pad[1]->test('Test'.$testFile);
            $this->assertEquals($x[0], $x[1]);
        }
        $this->assertGreaterThan(0, count($my_tests));
     }
    
}

