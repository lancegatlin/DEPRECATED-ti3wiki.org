<?php
/*

{{#for: <variable>=<begin>|to=<end>|step=<inc>]|repeat_text}}

%% is replaced in repeat text with % and prevents substition
%<variable> is replaced in repeat text with current value
%{ is replaced with {{
}% is replaced with }}


{{#fori: begin_value|end_value|step_value|repeat_text}}

%% is replaced in repeat text with % and prevents substition
%i is replaced in repeat text with current value
%{ is replaced with {{
}% is replaced with }}


{{#foreach: template_array|repeat_text}}

%% is replaced in repeat text with % and prevents substition
%i is replaced in repeat text with current value
%{ is replaced with {{
}% is replaced with }}
%a is replaced with the array name
%a[%i] is replaced with {{array|%i}}

*/

$wgExtensionFunctions[] = 'wfLoopFunctions';
$wgExtensionCredits['parserhook'][] = array(
'name' => 'LoopFunctions v0.1',
);

function wfLoopFunctions() {
    global $wgParser, $wgExtLoopFunctions, $wgStrPosMaxKeyLength;

    $wgExtLoopFunctions = new ExtLoopFunctions();
    $wgStrPosMaxKeyLength = 30;

    $wgParser->setFunctionHook( 'fori',       array( &$wgExtLoopFunctions, 'fori' ) );
    $wgParser->setFunctionHook( 'foreach',       array( &$wgExtLoopFunctions, '_foreach' ) );
    $wgParser->setFunctionHook( 'do_foreach',       array( &$wgExtLoopFunctions, 'do_foreach' ) );
    $wgParser->setFunctionHook( 'for',       array( &$wgExtLoopFunctions, '_for' ) );
}

class ExtLoopFunctions
{

    function replace_prenosubst($call)
    {
	$result = str_replace("%%","{!@$NOSUBsTMARKER!@$}",$call);
	return $result;
    }

	
    function replace_dosubst($call, $variable, $i)
    {
	$result = str_replace("%$variable",$i,$call);
	$result = str_replace("%{","{{",$result);
	$result = str_replace("}%","}}",$result);
	return $result;
    }

    function replace_postnosubst($call)
    {
	$result = str_replace("{!@$NOSUBsTMARKER!@$}","%",$call);
	return $result;
    }
	
    function replace($call, $variable, $i)
    {
	$result = $this->replace_prenosubst($call);
	$result = $this->replace_dosubst($result,$variable, $i);
	$result = $this->replace_postnosubst($result);
	return $result;
    }

    function fori( &$parser, $begin='0', $end='0', $step='1', $call='')
    {
	$numargs = func_num_args();
	for($i=5;$i<$numargs;$i++)
	{
		$call .= '|';
		$call .= func_get_arg($i);
	}

	$begin = $begin + 1 - 1; 
	$count = 0;
	$retv = '';
	for($i = $begin; $i <= $end; $i += $step)
	{
		$count += 1;
		if($count >= 256)
			break;
		$retv .= $this->replace($call,'i', $i);
	}

	return $retv;
    }
	
	
    function _for( &$parser, $parameters, $call)
    {
	$variable = $begin = $end = 'NULL';
	$step = 1;
	$scan_count = sscanf(trim($parameters), "%s to %d step %d", $variable, $end, $step);

	if($scan_count < 2)
		return "For[Error parsing '$parameters']";

	$args = explode("=",$variable);
	if($args === false || count($args) < 2)
		return "For[Error parsing '$parameters']";

	$variable = trim($args[0]);
	$begin = trim($args[1]);

	$numargs = func_num_args();
	for($i=3;$i<$numargs;$i++)
	{
		$call .= '|';
		$call .= func_get_arg($i);
	}

	$count = 0;
	$retv = '';
	for($i = $begin; $i <= $end; $i += $step)
	{
		$count += 1;
		if($count >= 256)
			break;
		$retv .= $this->replace($call, $variable, $i);
	}

	return $retv;
    }

    function _foreach( &$parser, $array='', $call='')
    {
	$numargs = func_num_args();
	for($i=3;$i<$numargs;$i++)
	{
		$call .= '|';
		$call .= func_get_arg($i);
	}
		
	if(strpos($array, 'Template') === 0)
	{
		$array_max_index = sprintf("{{#ifexist:%s|{{#expr: {{%s|size}}-1}}|-1}}", $array, $array);
	}
	else 
	{
		$array_max_index = sprintf("{{#ifexist:Template:%s|{{#expr: {{%s|size}}-1}}|-1}}", $array, $array);
	}

	return sprintf("{{#do_foreach: %s|0|%s|1|%s}}", $array, $array_max_index, $call);
    }

    function do_foreach( &$parser, $array, $begin='0', $end='0', $step='1', $call='')
    {
	if($begin > $end)
		return;

	$numargs = func_num_args();
	for($i=6;$i<$numargs;$i++)
	{
		$call .= '|';
		$call .= func_get_arg($i);
	}

	$begin = $begin + 1 - 1; 
	$count = 0;
	$retv = '';
	for($i = $begin; $i <= $end; $i += $step)
	{
		$count += 1;
		if($count >= 256)
			break;
		$result = $this->replace_prenosubst($call);
		$result = str_replace("%a[%i]",sprintf("{{%s|%d}}",$array,$i),$result);
		$result = str_replace("%a",$array,$result);
		$result = $this->replace_dosubst($result, 'i', $i);
		$result = $this->replace_postnosubst($result);
		$retv .= $result;
	}

	return $retv;
    }
}

?>