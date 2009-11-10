<?php
if (!defined('APP_PATH')) define('APP_PATH', realpath(dirname(__FILE__)) . '/../');
require_once APP_PATH . '/classes/Padawan_Creation.php';
require_once APP_PATH . '/classes/Padawan_Profiler.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

/**
 * Padawan_Creation test case.
 */
class Padawan_CreationTest extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @var Padawan_Creation
     */
    private $Padawan_Creation;

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
        // TODO Auto-generated Padawan_CreationTest::tearDown()
        $this->Padawan_Creation = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {    // TODO Auto-generated constructor
    }

    /**
     * Tests Padawan_Creation->__construct()
     */
    public function test__construct ()
    {
        $config = array();
        $config['debug'] = false;
        $this->Padawan_Creation = new Padawan_Creation($config);
        $this->assertTrue($this->Padawan_Creation->pp instanceof Padawan_Profiler);
    }

    /**
     * Tests Padawan_Creation->start()
     */
    public function testStart ()
    {
        // put info into tempdir
        $tmpname = serialize($_SERVER).time();
        $tmpname = "padawan_test_".md5($tmpname);
        $sys_tmp_dir = sys_get_temp_dir();
        $path_base= $sys_tmp_dir.DIRECTORY_SEPARATOR.$tmpname;
        
        $path_in  = $path_base.DIRECTORY_SEPARATOR."in".DIRECTORY_SEPARATOR;
        $path_out = $path_base.DIRECTORY_SEPARATOR."out".DIRECTORY_SEPARATOR;
        
        mkdir($path_base);
        mkdir($path_in);
        mkdir($path_out);
        
        $data_in_1 = '<?php
$abcdefghijklmnop = 3.14;
?>';
        $data_in_2 = '<?php
$abcdefghijklmno = 3.14;
?>';
        file_put_contents($path_in."LongVariable.php", $data_in_1);
        file_put_contents($path_in."LongVariable_ok.php", $data_in_2);
        file_put_contents($path_in."extension_test.php2", $data_in_2);
        
        // create object
        $config = array();
        $config['phc'] = trim(`which phc 2> /dev/null`);
        $config['skip_dot']    = true;
        $config['skip_xml']    = false;
        $config['extensions']  = array('php', 'php3', 'php4', 'php5', 'phtml');
        $config['pathInAbs']  = realpath($path_in);
        $config['pathOutAbs'] = realpath($path_out);
        
        $this->Padawan_Creation = new Padawan_Creation($config);
        $this->Padawan_Creation->start();
        echo $this->Padawan_Creation->pp->getProfiling();
        
        // verify file contents
        
        $read_1 = file_get_contents($path_out."LongVariable.xml");
        $read_2 = file_get_contents($path_out."LongVariable_ok.xml");
        
        unlink($path_out."LongVariable.xml");
        unlink($path_out."LongVariable_ok.xml");
        
        // this should not be created due to the extension check
        $u_x = 4;
        if (is_file($path_out."extension_test.xml")) {
        	$u_x = unlink($path_out."extension_test.xml");
        }
        rmdir($path_out);
        
        unlink($path_in."LongVariable.php");
        unlink($path_in."LongVariable_ok.php");
        unlink($path_in."extension_test.php2");
        rmdir($path_in);
        
        rmdir($path_base);
        
        $pat = '((.*)<attr key="phc.line_number"><integer>2</integer></attr>(.*)'.
        '<attr key="phc.unparser.source_rep"><string>3.14</string></attr>(.*))s';
        
        $pat_output = "(creating XML for 'LongVariable.php'...(.*)done(.*)".
                        "creating XML for 'LongVariable_ok.php'...(.*)done(.*)".
                        "Padawan: finished creating 2 XML files in ([0-9\.]+) sec(.*)".
                        "Padawan: total runtime: ([0-9\.]+) sec(.*))s";
        
        $this->assertRegExp($pat, $read_1);
        $this->assertRegExp($pat, $read_2);
        $this->expectOutputRegex($pat_output);
        $this->assertEquals($u_x, 4);
    }
}

