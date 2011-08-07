<?php
$wgExtensionFunctions[] = 'wfGameList';

/* register parser hook */
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'GameList',
    'author' => 'Lance Gatlin',
    'version' => '0.1',
);


function wfGameList() {
    global $wgParser,$gReplaceOnSaveHook;

	// Hook to for rending caching content
	$wgParser->setHook( 'gamelist', 'GameList_TagHook' );
//	$wgParser->setHook( 'testrevlist', 'GameList_TagTestHook' );
}

/*
<gamelist>
 <game name="" mainpage="" updatepage="" owner="user"/>
</gamelist>
*/
/*
function Gamelist_TagTestHook($content, $params, &$parser )
{
	$title = Title::newFromText($params['page']);
	if(!is_object($title))
		return 'error';
	// this is broken
	// see Revision::fetchFromConds it limits result to 1
	// just happens to be what we need to get the page creation timestamp
	$result = Revision::fetchAllRevisions($title);
	$retv .= 'rowCount=' . $result->numRows() . "\n";
	foreach($result as $row)
	{
		$retv .= '*' . $row->rev_id . ':' . $row->rev_timestamp . "\n";
	}
	return $parser->recursiveTagParse($retv);
//	return '<pre>' . print_r($results->result, true) . '</pre>';
	
}
*/
function Gamelist_TagHook($content, $params, &$parser )
{
//	$retv = '<table class=ti3pbem><tr><th>Game</th><th>Owner</th><th>Last Updated</th><th>Notes</th></tr>';
	$retv = "{| class=ti3pbem\n! Game\n! Owner\n! Last Updated\n! Created\n! Duration\n! Notes\n";

	$sort = $params['sort'];
	if(strlen($sort) == 0)
		$sort = 'lastupdate';
		

	$ptext = Parser::extractTagsAndParams(
					   array('game'),
					   $content,
					   $matches );	
	
	foreach($matches as $k => $i)
	{
		$tag = $i[0];
		$fulltag = $i[3];
		$content = $i[1];
		$params = $i[2];
		
		$name = $params['name'];
		$mainPage = $params['mainpage'];
		if(strlen($name) == 0)
			$name = $mainPage;
		$owner = $params['owner'];
		$updatePage = $params['updatepage'];
		
		$updateDate = $params['updatedate'];
		$createDate = $params['createdate'];
		
		$mainPageIsURL = strncmp($mainPage, 'http://', 7) == 0;
		if(!$mainPageIsURL)
		{
			$mainPageTitle = Title::newFromText($mainPage);
			if(!is_object($mainPageTitle))
			{
				$owner = isset($owner) ? $owner : 'Error';
	//			$games['z'][] = '<tr><td>' . $name . '</td><td>' . (isset($owner) ? $owner : 'Error') . '</td><td>Error accessing' . $mainPage . '</td></tr>';
				$games['ZZZ'][] = "|-\n| [[$mainPage|$name]]\n| [[User:$owner|$owner]]\n| Error\n|\n|\n| Error accessing $mainPage\n";
				continue;
			}			

			$mainPageRev = Revision::newFromTitle($mainPageTitle);
			if(!is_object($mainPageRev))
			{
				$owner = isset($owner) ? $owner : 'Error';
				$games['ZZZ'][] = "|-\n| [[$mainPage|$name]]\n| [[User:$owner|$owner]]\n| Error\n|\n|\n| Error accessing $mainPage\n";
				continue;
			}
			
			if(strlen($updateDate) == 0)
			{
				if(strlen($updatePage) != 0)
				{
					$updatePageTitle = Title::newFromText($updatePage);
					if(!is_object($updatePageTitle))
					{
						$games['ZZZ'][] = "|-\n| [[$mainPage|$name]]\n| [[User:$owner|$owner]]\n| Error\n|\n|\n| Error accessing $updatePage\n";
						continue;
					}			

					$updatePageRev = Revision::newFromTitle($updatePageTitle);
					if(!is_object($updatePageRev))
					{
						$games['ZZZ'][] = "|-\n| [[$mainPage|$name]]\n| [[User:$owner|$owner]]\n| Error\n|\n|\n| Error accessing $updatePage\n";
						continue;
					}
				}
				else
				{
					$updatePage = $mainPage;
					$updatePageTitle = $mainPageTitle;
					$updatePageRev = $mainPageRev;
				}
				
				$timeStamp = $updatePageRev->getTimestamp();
				$year = substr($timeStamp,0,4);
				$month = substr($timeStamp,4,2);
				$day = substr($timeStamp,6,2);
				$timeStampStr = "$month-$day-$year";
			}
			else
			{
				// mm-dd-yyyy
				$month = substr($updateDate,0,2);
				$day = substr($updateDate,3,2);
				$year = substr($updateDate,6,4);
				$timeStampStr = $updateDate;
			}
			
			$firstPage = Revision::newFromId(Revision::fetchAllRevisions($updatePageTitle)->fetchObject()->rev_id);
			if(strlen($owner) == 0)
				$owner = $firstPage->getUserText();
			
			if(strlen($createDate) == 0)
			{
				$createTimeStamp = $firstPage->getTimeStamp();
				$createYear = substr($createTimeStamp,0,4);
				$createMonth = substr($createTimeStamp,4,2);
				$createDay = substr($createTimeStamp,6,2);
				$createTimeStampStr = "$createMonth-$createDay-$createYear";
			
			}
			else
			{
				// mm-dd-yyyy
				$createMonth = substr($createDate,0,2);
				$createDay = substr($createDate,3,2);
				$createYear = substr($createDate,6,4);
				$createTimeStampStr = $createDate;
			}
				
			$durationInDays = gregoriantojd($month, $day, $year) - gregoriantojd($createMonth, $createDay, $createYear) . ' days';
		}
		else
		{
			$timeStampStr = '';
			$durationInDays = '';
			$createTimeStampStr = '';
		}
		
		switch($sort)
		{
			case 'lastupdate' :
				$key = $timeStamp;
				break;
			case 'owner' :
				$key = $owner;
				break;
			case 'name' :
				$key = $name;
				break;
		}
		$fmtMainPage = $mainPageIsURL ? (strcmp($mainPage,$name) == 0 ? "[$mainPage]" : "[$mainPage $name]") : "[[$mainPage|$name]]";
		$games[$key][] = "|-\n| $fmtMainPage\n| [[User:$owner|$owner]]\n| $timeStampStr\n| $createTimeStampStr\n| $durationInDays\n| $content\n";
	}
	
	switch($sort)
	{
		case 'lastupdate' :
			krsort($games);
			break;
		case 'owner' :
		case 'name' :
			ksort($games);
			break;
	}
	
	foreach($games as $timeStamp => $timeStampArray)
		foreach($timeStampArray as $i => $row)
			$retv .= $row;
		
//	$retv .= '</table>';
	$retv .= '|}';
	return $parser->recursiveTagParse($retv);
};

?>