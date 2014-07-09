<?php
/* FluxAdmin Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

// flux Latest Commits
$ffeed = file_get_contents('https://github.com/rathena/FluxCP/commits/master.atom');
if($ffeed) {
	$i = 0;
	$xml = new SimpleXmlElement($ffeed);
}
$fcommessage=NULL;
$ffetch = 1;
if(isset($xml)){
	if(isset($xml->entry)){
		foreach($xml->entry as $rssItem){
			$i++;
			if($i <= $ffetch){
				$frepourl= 'https://github.com/rathena/FluxCP';
				$freponame= 'FluxCP';
				$fcomauthor= $rssItem->author->name;
				$fcomlogin = $rssItem->author->uri;
				$fcommessage= $rssItem->title;
				$fcomhash= explode('/',$rssItem->id);
			}
		}
	} else {
		$fcommessage.= 'No entry tags.';
	}
} else {
	$fcommessage.= 'No XML';
}

// rA Latest Commits
$rbfeed = file_get_contents('https://github.com/rathena/rathena/commits/master.atom');
if($rbfeed) {
	$i = 0;
	$xml = new SimpleXmlElement($rbfeed);
}
$rbcommessage=NULL;
$rbfetch = 1;
if(isset($xml)){
	if(isset($xml->entry)){
		foreach($xml->entry as $rssItem){
			$i++;
			if($i <= $rbfetch){
				$rbrepourl= 'https://github.com/rathena/rathena';
				$rbreponame= 'rAthena';
				$rbcomauthor= $rssItem->author->name;
				$rbcomlogin= 'https://github.com/'.$rssItem->author->name;
				$rbcommessage= $rssItem->title;
				$rbcomhash= explode('/',$rssItem->id);
			}
		}
	} else {
		$rbcommessage = 'No entry tags.';
	}
} else {
	$rbcommessage = 'No XML';
}

?>