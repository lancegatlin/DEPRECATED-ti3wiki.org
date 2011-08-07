<?php
$wgExtensionFunctions[] = 'wfInclude';

/* register parser hook */
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Include',
    'author' => 'Lance Gatlin',
    'version' => '0.1',
);

function wfInclude() {
    global $wgParser;

	// Hook to for rending caching content
	$wgParser->setHook( 'include', 'Include_TagHook' );
}

function Include_TagHook($content, $params, &$parser )
{
	$pre = $params['pre'];

	if(strlen($params['pages'])>0)
	{
		$pages = explode(',', $params['pages']);
		if(!is_array($pages))
			$pages = array( $params['pages'] );
	}
	else
		$pages = array();
	
	if(strlen($params['page'])>0)
		$pages[] = $params['page'];	
	
	foreach($pages as $page)
	{
		$title = Title::newFromText($page);
		if(!is_object($title))
		{
			$retv .= "Unable to open $page.\n";
			continue;
		}
		list($content,$title) = $parser->fetchTemplateAndtitle($title);

		if($pre)
			$retv .= '<pre><nowiki>' . htmlentities($content) . '</nowiki></pre>';
		else
			$retv .= $parser->recursiveTagParse($content);
	}
	return $retv;
}
?>