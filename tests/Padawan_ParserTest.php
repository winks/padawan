<?php
if (!defined('APP_PATH')) define('APP_PATH', realpath(dirname(__FILE__)) . '/../');
require_once APP_PATH . '/classes/Padawan_Parser.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Padawan_Parser test case.
 */
class Padawan_ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Padawan_Parser
     */
    private $Padawan_Parser;

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
        // TODO Auto-generated Padawan_ParserTest::tearDown()
        $this->Padawan_Parser = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {}

    /**
     * Tests Padawan_Parser->parse()
     */
    public function testParse ()
    {
        // TODO Auto-generated Padawan_ParserTest->testParse()
        $this->markTestIncomplete(
                "parse test not implemented");
        $this->Padawan_Parser->parse(/* parameters */);
    }
}

