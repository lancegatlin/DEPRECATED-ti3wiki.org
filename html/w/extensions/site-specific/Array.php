<?php
/*

{{#array:
|<value1>
|<value2>
| ... 
|<valuen>
}}

translated into :

{{#switch:{{{1}}}
|0=<value1>
|1=<value2>
| ... 
|n-1=<valuen>
|size=n-1
}}

*/

$wgExtensionFunctions[] = 'wfArrayFunctions';
$wgExtensionCredits['parserhook'][] = array(
'name' => 'Array v0.1',
);

function wfArrayFunctions() {
    global $wgParser, $wgExtArrayFunctions, $wgStrPosMaxKeyLength;

    $wgExtArrayFunctions = new ExtArrayFunctions();
    $wgStrPosMaxKeyLength = 30;

    $wgParser->setFunctionHook( 'array',       array( &$wgExtArrayFunctions, '_array' ) );
}

class ExtArrayFunctions
{

    function _array( &$parser, $idx='')
    {
	$size = func_num_args() - 2;
	if(is_numeric($idx) === false)
	{
		if(preg_match("/size/",$idx))
		{
			return $size;
		}
		return sprintf("ArrayError[Index '%s' is not numeric]", $idx);
	}

	if($idx >= $size)
		return sprintf("ArrayError[Index '%d' out of range 0->%d]", $idx, $size-1);

	return rtrim(func_get_arg($idx + 2));
    }

}

?>