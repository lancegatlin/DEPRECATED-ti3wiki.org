<?php
$wgExtensionFunctions[] = 'wfForumFrame';

/* register parser hook */
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'ForumFrame',
    'author' => 'Lance Gatlin',
    'version' => '0.1',
);


function wfForumFrame() {
    global $wgParser,$gReplaceOnSaveHook;

	$wgParser->setHook( 'forum', 'ForumFrame_TagHook' );
}

function ForumFrame_TagHook($content, $params, &$parser )
{
  $num = htmlspecialchars($params['num']);

  if($params['latest'])
    $num .= '/10000#bottom';
    
  $style = htmlspecialchars($params['style']);
  $retv = 
"<iframe src =\"/forum/YaBB.pl?num=$num$latest\" style=\"$style\">
  <p>Your browser does not support iframes.</p>
</iframe>";

  return $retv;
};

?>