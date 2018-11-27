<?php
if (!defined('FLUX_ROOT')) exit;

$title = null;
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
}
?>
