<?php

$wgExtensionFunctions[] = 'wfMiscFunctions';
$wgExtensionCredits['parserhook'][] = array(
'name' => 'ti3wiki.org Misc v0.1',
);

$wgHooks['LanguageGetMagic'][]       = 'wfMisc_Magic';
function wfMisc_Magic(&$magicWords, $langCode ) {
	# Add the magic word
	# The first array element is case sensitive, in this case it is not case sensitive
	# All remaining elements are synonyms for our parser function
	#$magicWords['example'] = array( 0, 'example' );
	# unless we return true, other parser functions extensions won't get loaded.
	$magicWords['sum'] = array(0, 'sum');
	$magicWords['getSum'] = array(0, 'getSum');
	return true;
}

function wfMiscFunctions() {
    global $wgParser;

	$wgParser->setFunctionHook( 'sum', 'sumFunctionHook' );
	$wgParser->setFunctionHook( 'getSum', 'getSumFunctionHook' );
}
function getSumFunctionHook(&$parser)
{
	global $gSums;
	$args = func_get_args();
	$name = $args[1];
	
	$retv = $gSums[$name];

	if($args[2] == 'reset')
		$gSums[$name] = 0;
		
	return $retv;
}

function sumFunctionHook(&$parser)
{
	global $gSums;
	
	$args = func_get_args();
	$name = $args[1];
	$value = $args[2];
	
	if(strlen($name) != 0 && strlen($value) != 0)
	{
		if($value == 'reset')
			$gSums[$name] = 0;
		else
			$gSums[$name] += $value;
	}
	return '';
}

?>