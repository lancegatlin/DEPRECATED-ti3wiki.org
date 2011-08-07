<?php
if( !defined( 'MEDIAWIKI' ) ) {
  die( "This file is part of MediaWiki and is not a valid entry point\n" );
}
#install extension hook
$wgExtensionFunctions[] = "JS_Control_Tabs";

$wgExtensionCredits['JS_Control_Tabs'][]= array(
					  'name'         => 'Custom Tabbed Panels MediaWiki Mod', 
					  'url'          => 'http://livepipe.net/projects/control_tabs/', 
					  );

// Add JavaScript
function JS_Control_tabs() { 
	global $wgOut,$wgParser, $gTabsTag_scriptsAdded;
	
	$wgParser->setHook('tabs','tabsTag');
	$wgParser->setHook('navtabs','navtabsTag');
	$wgParser->setHook('tabscript','tabscriptTag');
  
  // Insert javascript script that hooks the java script
  $wgOut->addScript("<script type=\"text/javascript\" src=\"/extensions/tabs/prototype.js\"></script>\n");
  $wgOut->addScript("<script type=\"text/javascript\" src=\"/extensions/tabs/control.tabs.js\"></script>\n");

  // only add the tab script if a tab tag is encoutered
  $gTabsTag_scriptsAdded = false;
	return;
}

function tabsTag($text, $params, &$parser)
{
  global $wgOut, $gTabsTag_scriptsAdded;
  
/*
  This breaks after the first display since when shown from parser cache the scripts aren't added in properly
  
  // only add the tab script if a tab tag is encoutered
  if(!$gTabsTag_scriptsAdded)
  {
    // Insert javascript script that hooks the java script
    $wgOut->addScript("<script type=\"text/javascript\" src=\"/extensions/tabs/prototype.js\"></script>\n");
    $wgOut->addScript("<script type=\"text/javascript\" src=\"/extensions/tabs/control.tabs.js\"></script>\n");
    $gTabsTag_scriptsAdded = true;
  }
  */
  
	if($params['class'] == null)
		$params['class'] = 'tabs';
		
	$p = '';
	foreach($params as $k=>$v)
		$p .= ' ' . $k . '="' . $v . '"';
	
	$retv = "<ul$p>";
//	$retv[1] = "<script>document.write('<style>";
	
	$ptext = Parser::extractTagsAndParams(
					   array('tab')
					   ,$text
					   ,$matches );	
					   
	foreach($matches as $k => $i)
	{
		$tag = $i[0];
		$content = $i[1];
		$args = $i[2];
		$fulltag = $i[3];
		
		if($tag == 'tab')
		{
			$content = $parser->recursiveTagParse($content);
			$id = $args['id'] == null ? $content : $args['id'];
			$retv .= "<li><a href=\"#$id\">$content</a></li>";
/*			if($last_tab_id != null)
			{
				if($at_least_second_tab)
					$retv[1] .= ' ,';
				$retv[1] .= "#$id";
				$at_least_second_tab = true;
			}*/
			$last_tab_id = $id;
		}
/*		else
		if($tag == 'div')
		{
			if($args['id'] == null)
				$args['id'] = $last_tab_id;
			
			$p = '';
			foreach($args as $k=>$v)
				$p .= ' ' . $k . '="' . $v . '"';
				
			$retv[2] .= "<div$p>" . $parser->recursiveTagParse($content) . "</div>\n";
//			$retv[1] .= "\t<div$p>\n\t\t" . $content . "\n\t</div>\n";
		}*/
	}
	
	$retv .= "</ul>";
//	$retv[1] .= "{ display:none; }</style>');</script>";
//	$retv[3] = "<script type=\"text/javascript\">new Control.Tabs('" . $tab_id . "');</script>";
	
/*	foreach($retv as $v)
		$final .= $v;*/
	
	return $retv;
}

function navtabsTag($text, $params, &$parser)
{
	if($params['class'] == null)
		$params['class'] = 'tabs';
		
	$root = $params['navroot'];
	
	$p = '';
	foreach($params as $k=>$v)
	{
		if($k != 'navroot')
			$p .= ' ' . htmlentities($k) . '="' . htmlentities($v) . '"';
	}
	
	$retv = "<ul$p>";
	
	$ptext = Parser::extractTagsAndParams(
					   array('tab')
					   ,$text
					   ,$matches );	
					   
	foreach($matches as $k => $i)
	{
		$tag = $i[0];
		$content = $i[1];
		$args = $i[2];
		$fulltag = $i[3];
		
		if($tag == 'tab')
		{
			$link = $args['link'];
			$class = $args['class'];
			
			$p = '';
			foreach($args as $k1=>$v1)
			{
				if($k1 != 'link')
					$p .= ' ' . htmlentities($k1) . '="' . htmlentities($v1) . '"';
			}
				
			if(strlen($link) == 0)
				$link = $root . '_' . $content;
				
			if(preg_match('/^(http|https|ftp|mailto)\:/',$link) == 0)
				$link = "/index.php?title=$link";
			
			$link = htmlentities($link);
			$content = htmlentities($content);

			if($class == 'active')
				$retv .= "<li><a$p>$content</a></li>";
			else
			{
				$retv .= "<li><a href=\"$link\"$p>$content</a></li>";
			}
		}
	}
	
	$retv .= "</ul>";
	
	return $retv;
}

function tabscriptTag($text, $params, &$parser)
{
	$tab_id = htmlentities($params['id']);
	
	return "<script type=\"text/javascript\">new Control.Tabs('" . $tab_id . "');</script>";
}

?>