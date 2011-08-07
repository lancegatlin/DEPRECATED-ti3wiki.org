<?php
$wgExtensionFunctions[] = 'wfTestTag';

/* register parser hook */
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'TestTag',
    'author' => 'Lance Gatlin',
    'version' => '0.1',
);

/*
	 * @version     1.3.2
	 * @author      Aidan Lister < aidan@php.net>
	 * @author      Peter Waller < iridum@php.net>
	 * @link        http://aidanlister.com/repos/v/function.hexdump.php
	 * @param       string  $data        The string to be dumped
	 * @param       bool    $htmloutput  Set to false for non-HTML output
	 * @param       bool    $uppercase   Set to true for uppercase hex
	 * @param       bool    $return      Set to true to return the dump
	 */
function Test_hexdump($data, $htmloutput = true, $uppercase = false, $return = false)
{
	// Init
	$hexi   = '';
	$ascii  = '';
	$dump   = ($htmloutput === true) ? '<pre>' : '';
	$offset = 0;
	$len    = strlen($data);
 
	// Upper or lower case hexidecimal
	$x = ($uppercase === false) ? 'x' : 'X';
 
	// Iterate string
	for ($i = $j = 0; $i < $len; $i++)
	{
		// Convert to hexidecimal
		$hexi .= sprintf("%02$x ", ord($data[$i]));
 
		// Replace non-viewable bytes with '.'
		if (ord($data[$i]) >= 32) {
			$ascii .= ($htmloutput === true) ?
							htmlentities($data[$i]) :
							$data[$i];
		} else {
			$ascii .= '.';
		}
 
		// Add extra column spacing
		if ($j === 7) {
			$hexi  .= ' ';
			$ascii .= ' ';
		}
 
		// Add row
		if (++$j === 16 || $i === $len - 1) {
			// Join the hexi / ascii output
			$dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);
			
			// Reset vars
			$hexi   = $ascii = '';
			$offset += 16;
			$j      = 0;
			
			// Add newline            
			if ($i !== $len - 1) {
				$dump .= "\n";
			}
		}
	}
 
	// Finish dump
	$dump .= $htmloutput === true ? '</pre>' : '';
	$dump .= "\n";
 
	// Output method
if ($return === false) 
	{
		echo $dump;
		} 
else	{
		return $dump;
		}
}

function wfTestTag() {
    global $wgParser;

	// Hook to for rending caching content
	$wgParser->setHook( 'test', 'Test_TagHook' );
}

function Test_strcmp($s1, $s2)
{
	$stop = strlen($s1) > strlen($s2) ? strlen($s1) : strlen($s2); 
	for($i=0;$i<$stop;$i++)
		if($s1[$i] != $s2[$i])
			return array($i, $s1[$i], $s2[$i]);
	return false;
}

function Test_TagHook($content, $params, &$parser )
{
//	$local_parser = clone $parser;
	
	$testOnPass = isset($params['onpass']) ? $params['onpass'] : 'GO';
	$testOnFail = isset($params['onfail']) ? $params['onfail'] : 'NOGO';
	$showResultAll = $params['showresult'];
	
	Parser::extractTagsAndParams(
				   array('input','output'),
				   $content,
				   $matches );	
	
	foreach($matches as $k => $i)
	{
		list($tag, $content, $params, $fulltag) = $i;
		
		$testDesc = isset($params['desc']) ? $params['desc'] : $testDesc;
		$showResult = isset($params['showresult']) ? $params['showresult'] : $showResultAll;
		
		switch($tag)
		{
			case 'input':
				$lastInput = trim($content);
//				$lastOutput = $local_parser->parse( $content, $local_parser->mTitle, $local_parser->mOptions, true, false )->getText();
				$lastOutput = $parser->mStripState->unstripBoth($parser->recursiveTagParse($lastInput));
				$lastOutput = html_entity_decode($lastOutput);
				break;
			case 'output':
//				$result = Test_strcmp(preg_replace('/\s+/','',$lastOutput),preg_replace('/\s+/','',$content));
//				$parsedContent = $local_parser->parse( $content, $local_parser->mTitle, $local_parser->mOptions, true, false )->getText();
				$parsedContent = $parser->mStripState->unstripBoth($parser->recursiveTagParse(trim($content)));
				$parsedContent = html_entity_decode($parsedContent);
//				$parsedContent = $content;
				$result = Test_strcmp(trim($lastOutput),trim($parsedContent));
				if($result === false)
					$retv .= "*$testDesc=$testOnPass\n";
				else
				{
					list($where, $s1, $s2) = $result;
					$retv .= "*$testDesc=$testOnFail (difference at $where -> OUTPUT[$s1] != GO[$s2])";
				}
				
				if($showResult || $result !== false)
				{
					
					$retv .= "\nINPUT";
					$retv .= '<pre>' . $lastInput . '</pre>';
					$retv .= "\nOUTPUT";
					$retv .= '<pre>' . $lastOutput . '</pre>';
					$retv .= Test_hexdump($lastOutput, true, false, true);
					$retv .= "\nEXPECTED";
					$retv .= '<pre>' . $parsedContent . '</pre>';
					$retv .= Test_hexdump($parsedContent, true, false, true);
				}
				break;
		}
	}
	return $parser->recursiveTagParse($retv);
}

?>