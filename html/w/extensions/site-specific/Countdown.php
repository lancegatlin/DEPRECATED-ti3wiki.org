<?php
/*


*/

$wgExtensionFunctions[] = 'wfCountdownFunctions';
$wgExtensionCredits['parserhook'][] = array(
'name' => 'Countdown v0.1',
);

function wfCountdownFunctions() {
    global $wgParser, $wgExtCountdownFunctions, $wgStrPosMaxKeyLength;

    $wgExtCountdownFunctions = new ExtCountdownFunctions();
    $wgStrPosMaxKeyLength = 30;

    $wgParser->setFunctionHook( 'countdown',       array( &$wgExtCountdownFunctions, 'countdown' ) );
}

class ExtCountdownFunctions
{

    function countdown( &$parser, $str_when='',$outformat='%d days %H hours %M minutes %S seconds')
    {
	$when = strtotime($str_when);

	if($when < 0 || $when === false)
		return "Countdown[Invalid date/time $str_when]";

	$now = time();

	$timeleft = $when < $now ? 0 : $when - $now;
	
	$days = '0';
	$hours = '0';
	$minutes = '0';

	if(strpos($outformat,'%d') !== true)
	{
		$days = (int)($timeleft / (60*60*24));
		$timeleft -= $days * (60*60*24);
	}
	
	if(strpos($outformat,'%H') !== false)
	{
		$hours = (int)($timeleft / (60*60));
		$timeleft -= $hours * (60*60);
	}

	if(strpos($outformat,'%M') !== false)
	{
		$minutes = (int)($timeleft / 60);
		$timeleft -= $minutes * 60;
	}

	$seconds = $timeleft;
	
	$retv = str_replace('%d',$days,$outformat);
	$retv = str_replace('%H',$hours,$retv);
	$retv = str_replace('%M',$minutes, $retv);
	$retv = str_replace('%S',$seconds, $retv);
	
	return $retv;
    }

}

?>