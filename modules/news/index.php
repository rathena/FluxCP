<?php
if (!defined('FLUX_ROOT')) exit;
$newslimit = (int)Flux::config('CMSNewsLimit');
$newstype = (int)Flux::config('CMSNewsType');
if($newstype == '1'){
	$news = Flux::config('FluxTables.CMSNewsTable'); 
	$sql = "SELECT title, body, link, author, created, modified FROM {$server->loginDatabase}.$news ORDER BY id DESC LIMIT $newslimit";
	$sth = $server->connection->getStatement($sql);
	$sth->execute();
	$news = $sth->fetchAll();
} elseif($newstype == '2'){
	$content = file_get_contents(Flux::config('CMSNewsRSS'));
	if($content) {
		$i = 0;
		$xml = new SimpleXmlElement($content);
	}
} elseif($newstype == '3'){
} elseif($newstype == '4'){
	$ch = curl_init('https://www.facebook.com/feeds/page.php?format=rss20&id='. Flux::config('CMSNewsFbID'));
	curl_setopt( $ch, CURLOPT_POST, false );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$content = curl_exec( $ch );
	if($content) {
		$i = 0;
		$xml = new SimpleXmlElement($content);
	}
} elseif($newstype == '5'){

} else {exit('Check CMSNewsType configuration option..');}
?>
