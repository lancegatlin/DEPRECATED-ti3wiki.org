<?php
#
# add page-refresh tab
#
$wgHooks['SkinTemplateContentActions'][] = 'wfContentRefreshHook';
 
function wfContentRefreshHook( &$content_actions ) {
    global $wgRequest, $wgRequest, $wgTitle;
 
    $action = $wgRequest->getText( 'action' );
 
    if ( $wgTitle->getNamespace() != NS_SPECIAL ) {
        $content_actions['purge'] = array(
            'class' => false,
            'text' => wfMsg( 'refresh' ),
            'href' => $wgTitle->getLocalUrl( 'action=purge' )
        );
    }
    return true;
}
?>