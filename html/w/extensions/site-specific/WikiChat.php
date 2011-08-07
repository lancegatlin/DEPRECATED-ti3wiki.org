<?php
/**
* MediaWiki WikiChat extension
* See: http://www.mediawiki.org/wiki/Extension:Chat for installation
* Licenced under LGPL (http://www.gnu.org/copyleft/lesser.html)
* Author: http://www.wikichat.org/User:Firebreather
*/
 
require_once "$IP/phpfreechat-1.0-beta11/src/pfcglobalconfig.class.php";
require_once "$IP/phpfreechat-1.0-beta11/src/phpfreechat.class.php";
 
 
/* Global variables */
$wgAllowAnonUsers = false; # set to false to deny access to anonymous users
 
# message shown when denying anonymous users. Change if you want a different message. WikiText ok..
$wgDenyAccessMessage = 'You must [[Special:Userlogin|login]] to be allowed into this chatroom';
 
/* Extension variables */
$wgExtensionFunctions[] = 'wfSetupWikiChat';
$wgExtensionCredits['other'][] = array(
    'name' => 'WikiChat',
    'version' => '0.2.2, 2007-07-25',
    'author' => '[http://www.wikichat.org/User:Firebreather  User:Firebreather]',
    'url' => 'http://www.wikichat.org/',
    'description' => 'Adds a tab to each article that switches to a chatroom',
);
 
class WikiChat {
 
	// Constructor
	function WikiChat() {
		global $wgHooks;
 
		# Add all our needed hooks
		$wgHooks['UnknownAction'][] = $this;
		$wgHooks['SkinTemplateTabs'][] = $this;
	}
 
	function onUnknownAction($action, $article) {
		global $wgOut, $wgSitename, $wgCachePages, $wgUser, $wgTitle, $wgDenyAccessMessage, $wgAllowAnonUsers;
 
		$wgCachePages = false;
 
		if($action == 'chat') {
 
			$wgOut->setHTMLTitle("WikiChat");
			$wgOut->setPageTitle("WikiChat");
 
			if($wgUser->isAnon()) {
				if(!$wgAllowAnonUsers) {
					$wgOut->addWikiText($wgDenyAccessMessage);
				  	return false;
				}
				else {
					$nick = "Guest";
				}
			}
			else {
				$nick = $wgUser->getName();
			}
 
			$params["title"]         = "WikiChat";
			$params["nick"]          = $nick; // setup the initial nickname
			$params["serverid"]      = $wgSitename;
			$params["openlinknewwindow"] = true;
			$params["channels"] = array($wgTitle->getPrefixedText());
			$params["frozen_nick"]    = true;     // do not allow to change the nickname
			$params["shownotice"]     = 0;        // 0 = nothing, 1 = just nickname changes, 2 = connect/quit, 3 = nick + connect/quit
			$params["max_nick_len"]   = 30;       // nickname length could not be longer than 10 caracteres
			$params["max_text_len"]   = 300;      // a message cannot be longer than 50 caracteres
			$params["max_channels"]   = 3;        // limit the number of joined channels tab to 3
			$params["max_privmsg"]    = 1;        // limit the number of private message tab to 1
			$params["refresh_delay"]  = 2000;    // chat refresh speed is 2 secondes (2000ms)
			$params["max_msg"]        = 30;       // max message in the history is 15 (message seen when reloading the chat)
			$params["height"]         = "230px";  // height of chat area is 230px
			$params["width"]          = "800px";  // width of chat area is 800px
			$params["debug"]          = false;     // activate debug console
                        $params["timeout"]        = 600000;   // msecs until user is disconnected
 
			$pfc = new phpFreeChat($params);
        	$wgOut->addHTML($pfc->printChat(true));
        	$wgOut->addHTML('<br><br><p>Type /help for a list of all commands</p>');
 
       		return false;
       	}
		else {
       		return true;
       	}
	}
 
	function onSkinTemplateTabs( &$skin, &$content_actions ) {
		global $wgRequest;
		
		// FIX: Some skins don't have this set correctly?
		// Fatal error: Call to a member function getLocalURL() on a non-object in /home/tithrwik/public_html/extensions/misc/WikiChat.php on line 100
		if(!is_object($skin->mTitle))
			return true;
		
		$action = $wgRequest->getText( 'action' );
 
		$content_actions['chat'] = array(
							'class' => ($action == 'chat') ? 'selected' : false,
							'text' => "chat",
							'href' => $skin->mTitle->getLocalURL( 'action=chat' )
				);
 
		return true;
	}
 
	# Needed in some versions to prevent Special:Version from breaking
	function __toString() { return 'WikiChat'; }
 }
 
/* Global function */
# Called from $wgExtensionFunctions array when initialising extensions
function wfSetupWikiChat() {
	global $wgWikiChat;
	$wgWikiChat = new WikiChat();
}
 
?>
