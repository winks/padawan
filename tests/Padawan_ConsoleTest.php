<?php
if (!defined('APP_PATH')) define('APP_PATH', realpath(dirname(__FILE__)) . '/../');
require_once APP_PATH . '/classes/Padawan_Console.php';
require_once APP_PATH . '/classes/Padawan_Creation.php';
require_once APP_PATH . '/classes/Padawan_Profiler.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * Padawan_Console test case.
 */
class Padawan_ConsoleTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var Padawan_Console
     */
    private $Padawan_Console;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        $this->Padawan_Console = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {}

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecHelp ()
    {
        $argv = array('./padawan.php', '-h');
        $fix = array('code' => 0, 'value' => 'showHelpMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
        
        $argv = array('./padawan.php', '--help');
        $fix = array('code' => 0, 'value' => 'showHelpMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecVersion ()
    {
        $config = array();
        $argv = array('./padawan.php', '-V');
        $fix = array('code' => 0, 'value' => 'showVersionMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
        
        $argv = array('./padawan.php', '--version');
        $fix = array('code' => 0, 'value' => 'showVersionMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecTests ()
    {
        $argv = array('./padawan.php', '-l');
        $fix = array('code' => 0, 'value' => 'showTestsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecTags ()
    {
        $argv = array('./padawan.php', '-t');
        $fix = array('code' => 0, 'value' => 'showTagsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecCreate ()
    {
        $argv = array('./padawan.php', '-c');
        $fix = array('code' => 0, 'value' => 'doCreateMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

/**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecParse ()
    {
        $argv = array('./padawan.php', '-p');
        $fix = array('code' => 0, 'value' => 'doParseMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecMissing ()
    {
        $argv = array('./padawan.php');
        $fix = array('code' => 2, 'value' => 'showMissingParamsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->handleExec()
     */
    public function testHandleExecWrong ()
    {
        $argv = array('./padawan.php', '--very-wrong');
        $fix = array('code' => 1, 'value' => 'showWrongParamsMock');
        $this->Padawan_Console = new Padawan_Console_Mock($argv);
        $ret = $this->Padawan_Console->handleExec();
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan_Console->doCreate()
     */
    public function testDoCreateBinary ()
    {
        $argv = array('./padawan.php', '-c', '/tmp/in', '/tmp/out');
        $config = array();
        $config['phc'] = '/tmp/sure_no_binary_here.at_least_I_hope_so';
        
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doCreate();
        
        $this->assertEquals(3, $ret['code']);
        
    }

    /**
     * Tests Padawan_Console->doCreate()
     */
    public function testDoCreatePathIn ()
    {
        $argv = array('./padawan.php', '-c', '', '/tmp/out');
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doCreate();
        
        $this->assertEquals(4, $ret['code']);
    }

    /**
     * Tests Padawan_Console->doCreate()
     */
    public function testDoCreatePathOut ()
    {
        $argv = array('./padawan.php', '-c', sys_get_temp_dir(), '');
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doCreate();
        
        $this->assertEquals(5, $ret['code']);
        
    }

    /**
     * Tests Padawan_Console->doCreate()
     */
    /*public function testDoCreateExclude ()
    {
        $argv = array('./padawan.php', '-c', sys_get_temp_dir(), sys_get_temp_dir(),'--exclude');
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doCreate();
        
        $this->assertEquals(5, $ret['code']);
        
    }*/

    /**
     * Tests Padawan_Console->doParse()
     */
    public function testDoParse ()
    {
        $argv = array('./padawan.php', '-p', '/tmp/hopefully_not_a_real_dir');
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->doParse();
        
        $this->assertEquals(6, $ret['code']);
    }

    /**
     * Tests Padawan_Console->printOutput()
     */
    public function testPrintOutput ()
    {
        // TODO Auto-generated Padawan_ConsoleTest->testPrintOutput()
        $this->markTestIncomplete("testPrintOutput test not implemented");
        $argv = array('./padawan.php');
        $this->Padawan_Console = new Padawan_Console($argv);
        
        $test = array('code' => 0, 'value' => "foo");
        
        $dump = $this->Padawan_Console->printOutput($test);
        
        
        $this->expectOutputString($test['value']);
    }
    
    /**
     * Tests Padawan_Console->showMissingParams()
     */
    public function testShowMissingParams ()
    {
        $argv = array('./padawan.php');
        $this->Padawan_Console = new Padawan_Console($argv);
        
        $ret = $this->Padawan_Console->showMissingParams();
        
        $pat = sprintf('(%s: missing arguments(.*))',  $argv[0]); 
        
        $this->assertEquals(2, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
    }
    
/**
     * Tests Padawan_Console->showWrongParams()
     */
    public function testShowWrongParams ()
    {
        $argv = array('./padawan.php', '--very-wrong');
        $this->Padawan_Console = new Padawan_Console($argv);
        $ret = $this->Padawan_Console->showWrongParams();
        
        $pat = sprintf('(%s: unknown arguments(.*))',  $argv[0]); 
        
        $this->assertEquals(1, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
    }

    /**
     * Tests Padawan_Console->showHelp()
     */
    public function testShowHelpLong ()
    {
        $argv = array('./padawan.php');
        $pat = sprintf('((.*)Usage: %s \[ -l \] \[ -t \] \[--version \](.*))',  $argv[0]);
        $this->Padawan_Console = new Padawan_Console($argv);
        $ret = $this->Padawan_Console->showHelp();
        
        $this->assertEquals(0, $ret['code']);
        $this->assertRegExp($pat, $ret['value']);
        
    }

    /**
     * Tests Padawan_Console->showVersion()
     */
    public function testShowVersion ()
    {
        $argv = array('./padawan.php');
        $config = array('version' => 23);
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showVersion();
        
        $pat = sprintf('(PADAWAN %s - PHP AST-based Detection of Antipatterns,' 
                . ' Workarounds And general Nuisances)',
                $config['version']);
        
        $this->assertRegExp($pat, $ret['value']);
    }

    /**
     * Tests Padawan_Console->showTests()
     */
    public function testShowTests ()
    {
        $argv = array('./padawan.php');
        $config['patterns'] = array(
            'TestFoo' => array(
                'hint' => 'a Foo test',
            ),
            'TestBar' => array(
                'hint' => 'a Bar test',
            ),
        );
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showTests();
        
        $pat = '((.*)TestFoo(.*)- a Foo test(.*)TestBar(.*)- a Bar test(.*))s';
        
        $this->assertRegExp($pat, $ret['value']);
        
    }

    /**
     * Tests Padawan_Console->showTags()
     */
    public function testShowTags ()
    {
        $argv = array('./padawan.php');
        $config['patterns'] = array(
            'TestFoo' => array(
                'tags' => array('foo', 'baz'),
            ),
            'TestBar' => array(
                'tags' => array('bar', 'baz'),
            ),
        );
        $this->Padawan_Console = new Padawan_Console($argv, $config);
        $ret = $this->Padawan_Console->showTags();
        
        $pat = '((.*)available tags:(.*)bar baz foo(.*))s';
        
        $this->assertRegExp($pat, $ret['value']);
        
    }
}

class Padawan_Console_Mock extends Padawan_Console
{
    function showHelp()
    {
        return array('code' => 0, 'value' => "showHelpMock");
    }

    function showVersion()
    {
        return array('code' => 0, 'value' => "showVersionMock");
    }

    function showTests()
    {
        return array('code' => 0, 'value' => "showTestsMock");
    }

    function showTags()
    {
        return array('code' => 0, 'value' => "showTagsMock");
    }

    function doCreate()
    {
        return array('code' => 0, 'value' => "doCreateMock");
    }

    function doParse()
    {
        return array('code' => 0, 'value' => "doParseMock");
    }

    function showMissingParams()
    {
        return array('code' => 2, 'value' => "showMissingParamsMock");
    }

    function showWrongParams()
    {
        return array('code' => 1, 'value' => "showWrongParamsMock");
    }
}
