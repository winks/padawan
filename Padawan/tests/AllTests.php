<?php
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'Padawan_BaseTest.php';
require_once 'Padawan_ConsoleTest.php';
require_once 'Padawan_CreationTest.php';
require_once 'Padawan_ParserTest.php';
require_once 'Padawan_PatternTest.php';
require_once 'Padawan_ProfilerTest.php';

/**
 * Static test suite.
 */
class AllTests extends PHPUnit_Framework_TestSuite
{

    /**
     * Constructs the test suite handler.
     */
    public function __construct ()
    {
        if (!defined('APP_PATH')) define('APP_PATH', realpath(dirname(__FILE__)) . '/../');
        
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APP_PATH . '/classes'),
            realpath(APP_PATH . '/tests'),
            get_include_path(),
        )));
        
        $this->setName('AllTests');
        $this->addTestSuite('Padawan_BaseTest');
        $this->addTestSuite('Padawan_ConsoleTest');
        $this->addTestSuite('Padawan_CreationTest');
        $this->addTestSuite('Padawan_ParserTest');
        $this->addTestSuite('Padawan_PatternTest');
        $this->addTestSuite('Padawan_ProfilerTest');
    }

    /**
     * Creates the suite.
     */
    public static function suite ()
    {
        return new self();
    }
}

