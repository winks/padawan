<?php
if (!defined('APP_PATH')) define('APP_PATH', realpath(dirname(__FILE__)) . '/../');
require_once APP_PATH . '/classes/Padawan_Base.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Padawan test case.
 */
class Padawan_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Padawan
     */
    private $Padawan;

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
        $this->Padawan = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct ()
    {}

    /**
     * Tests Padawan->loadFile()
     */
    public function testLoadFileOk ()
    {
        $data = "eNrlVU1vozAQPdNfwXIHh6SnyKFipUitlFRVUlW9IYdMwFuwLduU7L9fgyGBZqtVVXWl1XIyw5uP94YZ45tjWbivIBXlbOGFwcS7ia5wvH2cP9w+JCqVVGjXYJiaG+PCy7UWc4Tqug5ELlJeClqADLjMkMhTPwxCr4Pf3m0+Al9/DH5UdASvZy1qOpmE6Hm92qY5lMSnTGnCUvCiKwcTraUyB3tyX+DnwjNRg4PJwUhpQFhpSVkWoZyXgA4EpXwPSJA9qQlDGpRWaMVZ9kQkJbsCmiIx6pwwasI2iVCfqdVxq4mGEphOCqp0m78xL19JkcBRNB7OoLiL8owKjbfy7FenK/IUrTGhC9upGufL+V5mKCiDhFXlDqRJQpmGDGQ0xag/DnzPanXCxErRjDWUB7FVz/TLqXyGy4iMZdPnGwbvyfwNNp+iM+ZjCT0SmYFZCYrOGS0WnpYVeC4aQp7izV38fbVM7uP1clSHOgEdbP7/CiKyS/dwyHL646UoGTcsrL3P/148O0NmfMZz8BtrF2PUB7zjvIjwN993qUokHFzfjw6kUIBR++ncwM0yXv2bzXvrXTFBpDLrVPFKpmBoi3OxsyC8fmcexv+AbU8DnwyfcHo9G/XOyn5Sz76OZ7tr13ARtpLfc/HfLMXZn5aiFclKYs9vLxRrPV/X0S9ppGu9";
        $file = tempnam(sys_get_temp_dir(), "padawan_basetest_");
        file_put_contents($file, gzuncompress(base64_decode($data)));
        
        $config = array();
        
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->loadFile($file);
        unlink($file);
        $this->assertTrue($ret);
        
    }

    /**
     * Tests Padawan->loadFile()
     */
    public function testLoadFileError ()
    {
        $data = "abcdef";
        $file = tempnam(sys_get_temp_dir(), "padawan_basetest_");
        file_put_contents($file, $data);
        chmod($file, 0000);
        
        $config = array();
        
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->loadFile($file);
        unlink($file);
        $this->assertFalse($ret);
    }

    /**
     * Tests Padawan->xpath()
     */
    public function testXpathOk ()
    {
        $config = array();
        $data = "eNrlVU1vozAQPdNfwXIHh6SnyKFipUitlFRVUlW9IYdMwFuwLduU7L9fgyGBZqtVVXWl1XIyw5uP94YZ45tjWbivIBXlbOGFwcS7ia5wvH2cP9w+JCqVVGjXYJiaG+PCy7UWc4Tqug5ELlJeClqADLjMkMhTPwxCr4Pf3m0+Al9/DH5UdASvZy1qOpmE6Hm92qY5lMSnTGnCUvCiKwcTraUyB3tyX+DnwjNRg4PJwUhpQFhpSVkWoZyXgA4EpXwPSJA9qQlDGpRWaMVZ9kQkJbsCmiIx6pwwasI2iVCfqdVxq4mGEphOCqp0m78xL19JkcBRNB7OoLiL8owKjbfy7FenK/IUrTGhC9upGufL+V5mKCiDhFXlDqRJQpmGDGQ0xag/DnzPanXCxErRjDWUB7FVz/TLqXyGy4iMZdPnGwbvyfwNNp+iM+ZjCT0SmYFZCYrOGS0WnpYVeC4aQp7izV38fbVM7uP1clSHOgEdbP7/CiKyS/dwyHL646UoGTcsrL3P/148O0NmfMZz8BtrF2PUB7zjvIjwN993qUokHFzfjw6kUIBR++ncwM0yXv2bzXvrXTFBpDLrVPFKpmBoi3OxsyC8fmcexv+AbU8DnwyfcHo9G/XOyn5Sz76OZ7tr13ARtpLfc/HfLMXZn5aiFclKYs9vLxRrPV/X0S9ppGu9";
        $data = gzuncompress(base64_decode($data));
        $xpath = '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]';
        $test = Padawan::TEST_COUNT;
        $expected = 1;
        
        $this->Padawan = new Padawan($config);
        $this->Padawan->setXml($data);
        $ret = $this->Padawan->xpath($xpath, $test, $expected);
        $this->assertTrue($ret);
    }

    /**
     * Tests Padawan->xpath()
     */
    public function testXpathError ()
    {
        $config = array();
        $data = "eNrlVU1vozAQPdNfwXIHh6SnyKFipUitlFRVUlW9IYdMwFuwLduU7L9fgyGBZqtVVXWl1XIyw5uP94YZ45tjWbivIBXlbOGFwcS7ia5wvH2cP9w+JCqVVGjXYJiaG+PCy7UWc4Tqug5ELlJeClqADLjMkMhTPwxCr4Pf3m0+Al9/DH5UdASvZy1qOpmE6Hm92qY5lMSnTGnCUvCiKwcTraUyB3tyX+DnwjNRg4PJwUhpQFhpSVkWoZyXgA4EpXwPSJA9qQlDGpRWaMVZ9kQkJbsCmiIx6pwwasI2iVCfqdVxq4mGEphOCqp0m78xL19JkcBRNB7OoLiL8owKjbfy7FenK/IUrTGhC9upGufL+V5mKCiDhFXlDqRJQpmGDGQ0xag/DnzPanXCxErRjDWUB7FVz/TLqXyGy4iMZdPnGwbvyfwNNp+iM+ZjCT0SmYFZCYrOGS0WnpYVeC4aQp7izV38fbVM7uP1clSHOgEdbP7/CiKyS/dwyHL646UoGTcsrL3P/148O0NmfMZz8BtrF2PUB7zjvIjwN993qUokHFzfjw6kUIBR++ncwM0yXv2bzXvrXTFBpDLrVPFKpmBoi3OxsyC8fmcexv+AbU8DnwyfcHo9G/XOyn5Sz76OZ7tr13ARtpLfc/HfLMXZn5aiFclKYs9vLxRrPV/X0S9ppGu9";
        $data = gzuncompress(base64_decode($data));
        $xpath = '';
        $test = Padawan::TEST_COUNT;
        $expected = 1;
        
        $this->Padawan = new Padawan($config);
        $this->Padawan->setXml($data);
        $ret = $this->Padawan->xpath($xpath, $test, $expected);
        $this->assertFalse($ret);
    }

    /**
     * Tests Padawan->test()
     */
    public function testTestCount ()
    {
        $config = array(
            'TestLongVariable' => array(
                'query' => '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]',
                'test' => Padawan::TEST_COUNT,
                'expected' => 1,
                'hint' => 'long variable names (> 15 chars)',
                'tags' => array('cs'), 
            ),
        );
        
        $data = "eNrlVU1vozAQPdNfwXIHh6SnyKFipUitlFRVUlW9IYdMwFuwLduU7L9fgyGBZqtVVXWl1XIyw5uP94YZ45tjWbivIBXlbOGFwcS7ia5wvH2cP9w+JCqVVGjXYJiaG+PCy7UWc4Tqug5ELlJeClqADLjMkMhTPwxCr4Pf3m0+Al9/DH5UdASvZy1qOpmE6Hm92qY5lMSnTGnCUvCiKwcTraUyB3tyX+DnwjNRg4PJwUhpQFhpSVkWoZyXgA4EpXwPSJA9qQlDGpRWaMVZ9kQkJbsCmiIx6pwwasI2iVCfqdVxq4mGEphOCqp0m78xL19JkcBRNB7OoLiL8owKjbfy7FenK/IUrTGhC9upGufL+V5mKCiDhFXlDqRJQpmGDGQ0xag/DnzPanXCxErRjDWUB7FVz/TLqXyGy4iMZdPnGwbvyfwNNp+iM+ZjCT0SmYFZCYrOGS0WnpYVeC4aQp7izV38fbVM7uP1clSHOgEdbP7/CiKyS/dwyHL646UoGTcsrL3P/148O0NmfMZz8BtrF2PUB7zjvIjwN993qUokHFzfjw6kUIBR++ncwM0yXv2bzXvrXTFBpDLrVPFKpmBoi3OxsyC8fmcexv+AbU8DnwyfcHo9G/XOyn5Sz76OZ7tr13ARtpLfc/HfLMXZn5aiFclKYs9vLxRrPV/X0S9ppGu9";
        $data = gzuncompress(base64_decode($data));
        
        $this->Padawan = new Padawan($config);
        $this->Padawan->setXml($data);
        $ret = $this->Padawan->test('TestLongVariable');
        $this->assertTrue($ret);
    }
    
/**
     * Tests Padawan->test()
     */
    public function testTestCountDetails ()
    {
        $config = array(
            'TestLongVariable' => array(
                'query' => '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]',
                'test' => Padawan::TEST_COUNT,
                'expected' => 1,
                'hint' => 'long variable names (> 15 chars)',
                'tags' => array('cs'), 
            ),
        );
        
        $data = "eNrlVU1vozAQPdNfwXIHh6SnyKFipUitlFRVUlW9IYdMwFuwLduU7L9fgyGBZqtVVXWl1XIyw5uP94YZ45tjWbivIBXlbOGFwcS7ia5wvH2cP9w+JCqVVGjXYJiaG+PCy7UWc4Tqug5ELlJeClqADLjMkMhTPwxCr4Pf3m0+Al9/DH5UdASvZy1qOpmE6Hm92qY5lMSnTGnCUvCiKwcTraUyB3tyX+DnwjNRg4PJwUhpQFhpSVkWoZyXgA4EpXwPSJA9qQlDGpRWaMVZ9kQkJbsCmiIx6pwwasI2iVCfqdVxq4mGEphOCqp0m78xL19JkcBRNB7OoLiL8owKjbfy7FenK/IUrTGhC9upGufL+V5mKCiDhFXlDqRJQpmGDGQ0xag/DnzPanXCxErRjDWUB7FVz/TLqXyGy4iMZdPnGwbvyfwNNp+iM+ZjCT0SmYFZCYrOGS0WnpYVeC4aQp7izV38fbVM7uP1clSHOgEdbP7/CiKyS/dwyHL646UoGTcsrL3P/148O0NmfMZz8BtrF2PUB7zjvIjwN993qUokHFzfjw6kUIBR++ncwM0yXv2bzXvrXTFBpDLrVPFKpmBoi3OxsyC8fmcexv+AbU8DnwyfcHo9G/XOyn5Sz76OZ7tr13ARtpLfc/HfLMXZn5aiFclKYs9vLxRrPV/X0S9ppGu9";
        $data = gzuncompress(base64_decode($data));
        
        $fix = array(
            0 => '/home/fa/code/padawan/tests/LongVariable.php',
            1 => 2,
        );
        
        $this->Padawan = new Padawan($config);
        $this->Padawan->setXml($data);
        $ret = $this->Padawan->test('TestLongVariable', true);
        $this->assertEquals($fix, $ret);
    }

    /**
     * Tests Padawan->getHint()
     */
    public function testGetHint ()
    {
        $config = array(
            'TestLongVariable' => array(
                'query' => '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]',
                'test' => Padawan::TEST_COUNT,
                'expected' => 1,
                'hint' => 'long variable names (> 15 chars)',
                'tags' => array('cs'), 
            ),
        );
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->getHint('TestLongVariable');
        $this->assertEquals('Found '.$config['TestLongVariable']['hint'], $ret);
    }

    /**
     * Tests Padawan->getConfig()
     */
    public function testGetConfig ()
    {
        $config = array(
            'TestLongVariable' => array(
                'query' => '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]',
                'test' => Padawan::TEST_COUNT,
                'expected' => 1,
                'hint' => 'long variable names (> 15 chars)',
                'tags' => array('cs'), 
            ),
        );
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->getConfig();
        $this->assertEquals($config, $ret);
    }

    /**
     * Tests Padawan->getConfig()
     */
    public function testSetXmlOk ()
    {
        $config = array();
        $data =<<<EOF
<?xml version="1.0"?>
<AST:PHP_script xmlns:AST="http://www.phpcompiler.org/phc-1.1" xmlns:HIR="http://www.phpcompiler.org/phc-1.1" xmlns:MIR="http://www.phpcompiler.org/phc-1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<attrs>
		<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
	</attrs>
	<AST:Statement_list>
		<AST:Eval_expr>
			<attrs>
				<attr key="phc.comments">
					<string_list>
					</string_list>
				</attr>
				<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
				<attr key="phc.line_number"><integer>2</integer></attr>
			</attrs>
			<AST:Assignment>
				<attrs>
					<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
					<attr key="phc.line_number"><integer>2</integer></attr>
				</attrs>
				<AST:Variable>
					<attrs>
						<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
						<attr key="phc.line_number"><integer>2</integer></attr>
					</attrs>
					<AST:Target xsi:nil="true" />
					<AST:VARIABLE_NAME>
						<attrs />
						<value>a</value>
					</AST:VARIABLE_NAME>
					<AST:Expr_list>
					</AST:Expr_list>
				</AST:Variable>
				<bool><!-- is_ref -->false</bool>
				<AST:BOOL>
					<attrs>
						<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
						<attr key="phc.line_number"><integer>2</integer></attr>
						<attr key="phc.unparser.source_rep"><string>true</string></attr>
					</attrs>
					<value>True</value>
				</AST:BOOL>
			</AST:Assignment>
		</AST:Eval_expr>
		<AST:Nop>
			<attrs>
				<attr key="phc.comments">
					<string_list>
					</string_list>
				</attr>
				<attr key="phc.filename"><string>/tmp/xml/xmltest.php</string></attr>
				<attr key="phc.line_number"><integer>3</integer></attr>
			</attrs>
		</AST:Nop>
	</AST:Statement_list>
</AST:PHP_script>
EOF;
        
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->setXml($data);
        $this->assertTrue($ret);
    }
    

    /**
     * Tests Padawan->getConfig()
     */
    public function testSetXmlError ()
    {
        $config = array();
        $data = "</AST:PHP_script>";
        
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->setXml($data);
        $this->assertFalse($ret);
    }
    /**
     * Tests Padawan->getConfig()
     */
    public function testSetXmlShort ()
    {
        $config = array();
        $data = ' xmlns="http://www.phpcompiler.org/phc-1.0"';
        
        $this->Padawan = new Padawan($config);
        $ret = $this->Padawan->setXml($data);
        $this->assertFalse($ret);
    }
}

