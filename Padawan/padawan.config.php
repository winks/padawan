<?php
/**
 * PADAWAN - PHP AST-based Detection of Antipatterns, Workarounds And general Nuisances
 * 
 * @package    Padawan
 * @author     Florian Anderiasch, <anderiasch at mayflower.de>
 * @copyright  2007-2009 Mayflower GmbH, www.mayflower.de
 * @version    $Id:$
 */
// uncomment the line below if "which" doesn't find phc (http://phpcompiler.org)
$padawan['phc'] = trim(`which phc 2> /dev/null`);
//$padawan['phc'] = '/path/to/phc';

$padawan['skip_dot']    = false;
$padawan['skip_xml']    = false;
$padawan['extensions']  = array('php', 'php3', 'php4', 'php5', 'phtml');

$padawan['patterns'] = array(
/*
'TestEmptyIf' => array(
        'query' => '//AST:If/AST:Statement_list[position()=1 and count(*)<2]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'empty if-expression',
        'hint2' => 'if(<EXPR>) {<empty>}',
        'tags' => array('potential'), 
    ),

    'TestEmptyElse' => array(
        'query' => '//AST:If/AST:Statement_list[position()=2 and count(*)<2]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'missing else{} in if-expression',
        'hint2' => 'if(<EXPR>) {<EXPR>} else {<empty>}',
        'tags' => array('cs'), 
    ),
*/
    'TestEmptyTry' => array(
        'query' => '//AST:Try/AST:Statement_list[count(*)<1]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'empty try-expression',
        'hint2' => 'try {<empty>}',
        'tags' => array('potential'), 
    ),
    'TestEmptyCatch' => array(
        'query' => '//AST:Try/AST:Catch_list/AST:Catch/AST:Statement_list[count(*)<1]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'empty catch{} in try-expression',
        'hint2' => 'try {<EXPR>} catch(Exception $e) {<empty>}',
        'tags' => array('potential'), 
    ),
    'TestDefineVar' => array(
        'query' => '//AST:Method_invocation/AST:Actual_parameter_list[preceding-sibling::AST:METHOD_NAME/value[text()="define"]]/AST:Actual_parameter[2]/AST:Variable',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'define() with variable instead of constant/literal',
        'hint2' => 'define(\'SOME_NAME\', $var)',
        'tags' => array('security'), 
    ),
    'TestInclude' => array(
        'query' => '//AST:Method_invocation/AST:Actual_parameter_list[preceding-sibling::AST:METHOD_NAME/value[text()="require" or text()="require_once" or text()="include" or text()="include_once" or text()="readfile" or text()="virtual" or text()="file_get_contents" or text()="fopen" or text()="file" or text()="mysql_query"]]/AST:Actual_parameter/AST:Variable/AST:VARIABLE_NAME/value[text()="_GET" or text()="_POST" or text()="_REQUEST"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'unfiltered function call with $_GET|$_POST|$_REQUEST (include[_once]|require[_once]|readfile|virtual|file_get_contents|fopen|file|mysql_query)',
        'hint2' => 'include[_once]|require[_once]|readfile|virtual|file_get_contents|fopen|file|mysql_query($_GET|$_POST|$_REQUEST)',
        'tags' => array('security', 'php'), 
    ),
    'TestIncludeCat' => array(
        'query' => '//AST:Variable[ancestor::AST:Method_invocation/AST:Actual_parameter_list[preceding-sibling::AST:METHOD_NAME/value[text()="require" or text()="require_once" or text()="include" or text()="include_once" or text()="readfile" or text()="virtual" or text()="file_get_contents" or text()="fopen" or text()="file" or text()="mysql_query"]]/AST:Actual_parameter]/AST:VARIABLE_NAME/value[text()="_GET" or text()="_POST" or text()="_REQUEST"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'unfiltered function call with $_GET|$_POST|$_REQUEST (include[_once]|require[_once]|readfile|virtual|file_get_contents|fopen|file|mysql_query)',
        'hint2' => 'include[_once]|require[_once]|readfile|virtual|file_get_contents|fopen|file|mysql_query($_GET|$_POST|$_REQUEST)',
        'tags' => array('security','php'),
    ),
    'TestEvilFunctions' => array(
        'query' => '//AST:Method_invocation/AST:METHOD_NAME/value[text()="eval" or text()="exec" or text()="popen" or text()="proc_open" or text()="passthru" or text()="shell_exec" or text()="system"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'possibly dangerous function calls (eval|exec|popen|proc_open|passthru|shell_exec|system)',
        'hint2' => 'eval|exec|popen|proc_open|passthru|shell_exec|system(<EXPR>)',
        'tags' => array('security'), 
    ),
    'TestUpload' => array(
        'query' => '//AST:Method_invocation/AST:Actual_parameter_list[preceding-sibling::AST:METHOD_NAME/value[not(text()="move_uploaded_file")]]/AST:Actual_parameter/AST:Variable/AST:Expr_list[preceding-sibling::AST:VARIABLE_NAME/value[text()="_FILES"]]/AST:STRING[2]/value[text()="tmp_name"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'call to move_uploaded_file with wrong argument',
        'hint2' => 'move_uploaded_file(<EXPR>) with EXPR != $_FILES[EXPR][\'tmp_name\']',
        'tags' => array('security', 'php'), 
    ),
    'TestIfOrder' => array(
        'query' => '//AST:If/AST:Bin_op/AST:OP[preceding-sibling::AST:BOOL]/value[text()="==="]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'coding style: if-expression in wrong format',
        'hint2' => 'if(true|false === <EXPR>) {<EXPR>}',
        'tags' => array('cs'), 
    ),
    'TestOrExpression' => array(
        'query' => '//AST:Bin_op[not(parent::AST:If)]/AST:OP[preceding-sibling::AST:Method_invocation or following-sibling::AST:Method_invocation]/value[text()="OR" or text()="AND"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'use of "or"',
        'hint2' => '<EXPR> or <EXPR> outside if (<EXPR>)',
        'tags' => array('cs'), 
    ),
    'TestLoop' => array(
        'query' => '//AST:Bin_op[parent::AST:For or parent::AST:While or parent::AST:Do]/AST:Method_invocation/AST:METHOD_NAME/value[text()="count" or text()="max" or text()="min"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'unnecessary repeated calls to count|max|min in loops',
        'hint2' => 'for($i=<INT>;$i <BOOL> count|max|min(<EXPR>);$i++/$i--) / while ($i<BOOL> count|max|min(<EXPR>)) / do {} while($i <BOOL> count|max|min(<EXPR>))',
        'tags' => array('performance'), 
    ),
    'TestThis' => array(
        'query' => '//AST:Assignment[ancestor::AST:Class_def]/AST:Variable[child::AST:VARIABLE_NAME/value[text()="this"]]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'assignment to $this',
        'hint2' => '$this = <EXPR>',
        'tags' => array('potential'), 
    ),
    'TestUnsetThis' => array(
        'query' => '//AST:METHOD_NAME[parent::AST:Method_invocation/AST:Actual_parameter_list/AST:Actual_parameter/AST:Variable/AST:VARIABLE_NAME/value[text()="this"]]/value[text()="unset"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'use of unset() on $this',
        'hint2' => 'unset($this)',
        'tags' => array('potential'), 
    ),
    'TestTypecast' => array(
        'query' => '//AST:If/AST:Variable/AST:VARIABLE_NAME/value[text()="_GET" or text()="_POST" or text()="_REQUEST" or text()="_SESSION" or text()="_COOKIE"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'missing isset() in if-expression with $_GET|_POST|_REQUEST|_SESSION|_COOKIE[\'x\'] (problem with 0 == false)',
        'hint2' => 'if($_GET|_POST|_REQUEST|_SESSION|_COOKIE[\'x\']) {<EXPR>} which typecasts to false if $var == 0',
        'tags' => array('potential'), 
    ),
/*    'TestForeach' => array(
        'query' => '//AST:Foreach/bool[text()="true"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 0,
        'hint' => 'use of references in foreach()',
        'hint2' => 'foreach($var as &$ref)'
    ),*/
    'TestForeachKey' => array(
        'query' => array(
            array(
                'query' => '//AST:Foreach/AST:Variable[2]/AST:VARIABLE_NAME/value'
            ),
            array(
                'query' => '//AST:Variable[not(parent::AST:Foreach)]/AST:VARIABLE_NAME[ancestor::AST:Foreach[child::AST:Variable[2]/AST:VARIABLE_NAME/value[text()="%1$s"]]/AST:Statement_list]/value[text()="%1$s"]'
            )
        ),
        'test' => Padawan::TEST_STEP,
        'expected' => 1,
        'hint' => 'foreach($var as $key => $val) with unused $key',
        'hint2' => 'foreach($var as $key => $val) with unused $key',
        'tags' => array('performance', 'php'), 
    ),
    'TestUnconditionalIf' => array(
        'query' => '//AST:If/AST:BOOL/value[text()="True" or text()="False"]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'unconditional if (always true/always false)',
        'hint2' => 'if (true|false)',
        'tags' => array('potential'), 
    ),
    'TestShortVariable' => array(
        'query' => '//AST:Variable[not(parent::AST:For) and not(parent::AST:Do) and not(parent::AST:While) and not(parent::AST:Catch)]/AST:VARIABLE_NAME/value[string-length()<3]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'short variable names (< 3 chars)',
        'tags' => array('cs'), 
    ),
    'TestLongVariable' => array(
        'query' => '//AST:Variable/AST:VARIABLE_NAME/value[string-length()>15]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'long variable names (> 15 chars)',
        'tags' => array('cs'), 
    ),
    'TestShortMethod' => array(
        'query' => '//AST:Class_def/AST:Member_list/AST:Method/AST:Signature/AST:METHOD_NAME/value[string-length()<3]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'short method names (< 3 chars)',
        'tags' => array('cs'), 
    ),
    'TestSelectAll' => array(
        'query' => '//AST:Method_invocation[child::AST:METHOD_NAME/value[text()="mysql_query"]]/AST:Actual_parameter_list/AST:Actual_parameter/AST:STRING/value[contains(translate(text(),"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"select *")]',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => 'SQL statements containing "SELECT *"',
        'tags' => array('performance'), 
    ),
    /*
    'Test' => array(
        'query' => '',
        'test' => Padawan::TEST_COUNT,
        'expected' => 1,
        'hint' => '<EXPR>'
    ),
    */
);

$padawan['version'] = "0.4.3";
$padawan['debug'] = false;
?>
