<?php
require_once 'classes/profiler.php';
/**
 * Padawan_Profiler test case.
 */
class Padawan_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Padawan_Profiler
     */
    private $Padawan_Profiler;
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->Padawan_Profiler = new Padawan_Profiler();
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Padawan_Profiler = null;
        parent::tearDown();
    }
    /**
     * Constructs the test case.
     */
    public function __construct ()
    {// TODO Auto-generated constructor
    }
    /**
     * Tests Padawan_Profiler->up()
     */
    public function testUp ()
    {
        for ($index = 0; $index < 15;$index++) {
            $this->Padawan_Profiler->up('dot');
            $xd = $this->Padawan_Profiler->getRaw('dot');
            $this->assertEquals($xd['num'], $index+1);
            
            $this->Padawan_Profiler->up('xml');
            $xx = $this->Padawan_Profiler->getRaw('xml');
            $this->assertEquals($xd['num'], $index+1);
        }
    }
    /**
     * Tests Padawan_Profiler->profile()
     */
    public function testProfile ()
    {
        $this->Padawan_Profiler->profile('dot');
        sleep(1);
        $this->Padawan_Profiler->profile('dot', true);
        $xp = $this->Padawan_Profiler->getRaw('dot');
        $this->assertGreaterThan(0, $xp['time']);
        
        $this->Padawan_Profiler->profile('xml');
        sleep(1);
        $this->Padawan_Profiler->profile('xml', true);
        $xp = $this->Padawan_Profiler->getRaw('xml');
        $this->assertGreaterThan(0, $xp['time']);
    }
    /**
     * Tests Padawan_Profiler->getProfiling()
     */
    public function testGetProfiling ()
    {
        $this->Padawan_Profiler->up('dot');
        $xd = $this->Padawan_Profiler->getRaw('dot');
        
        $this->Padawan_Profiler->up('xml');
        $xx = $this->Padawan_Profiler->getRaw('xml');
        
        $xp = $this->Padawan_Profiler->getProfiling('xml');
        
        $this->assertRegExp('/Padawan: (.*)finished creating ([0-9]+) DOT files in ([0-9\.]+) sec/', $xp);
        $this->assertRegExp('/Padawan: (.*)finished creating ([0-9]+) XML files in ([0-9\.]+) sec/', $xp);
        $this->assertRegExp('/Padawan: (.*)total runtime: ([0-9\.]+) sec/', $xp);
    }
    /**
     * Tests Padawan_Profiler->getRaw()
     */
    public function testGetRaw ()
    {
        $fixture_bogus = array();
        $my_bogus = $this->Padawan_Profiler->getRaw('xyz');
        $this->assertEquals($fixture_bogus, $my_bogus);
        
        $fixture_xml = array('num' => 0, 'time' => 0, 'start' => 0);
        $my_xml = $this->Padawan_Profiler->getRaw('xml');
        $this->assertEquals($fixture_xml, $my_xml);
        
        $this->Padawan_Profiler->up('dot');
        $fixture_dot = array('num' => 1, 'time' => 0, 'start' => 0);
        $my_dot = $this->Padawan_Profiler->getRaw('dot');
        $this->assertEquals($fixture_dot, $my_dot);
    }
}

