<?php
$wgExtensionFunctions[] = "wfMeeboExtension";

function wfMeeboExtension() {
   global $wgParser;

   $wgParser->setHook( "meebo", "MeeboTagExtension" );
}

function MeeboTagExtension( $input, $argv, &$parser ) 
{
  $id = htmlentities($argv['id']);
	$retv = 
"<!-- Beginning of meebo me widget code. Want to talk with visitors on your page? Go to http://www.meebome.com/ and get your widget! --> <object width=\"190\" height=\"275\" ><param name=\"movie\" value=\"http://widget.meebo.com/mm.swf?$id\"/><embed src=\"http://widget.meebo.com/mm.swf?$id\" type=\"application/x-shockwave-flash\" width=\"190\" height=\"275\"></embed></object>";
	return $retv;
}
?>
