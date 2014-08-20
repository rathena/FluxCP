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
	$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, 'https://www.facebook.com/feeds/page.php?format=rss20&id='. Flux::config('CMSNewsFbID'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //set this flag for results to the variable
    curl_setopt($ch, CURLOPT_POST, 0);           //if you're making a post, put the data here
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //This is required for HTTPS certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //you don't have some key/password action
    /* execute the request */
    $content = curl_exec($ch);
    curl_close($ch);
	if($content) {
		$i = 0;
		$xml = new SimpleXmlElement($content);
	}
} elseif($newstype == '5'){

} else {exit('Check CMSNewsType configuration option..');}
?>
