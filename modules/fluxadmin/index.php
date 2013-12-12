<?php
/* FluxAdmin Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

// FluxCP Pull Request Data
$fluxprfeed = file_get_contents('https://zapier.com/engine/rss/144953/fluxcp/');
if($fluxprfeed) {
	$i = 0;
	$xml = new SimpleXmlElement($fluxprfeed);
}
$fpulldisplay=NULL;
$fluxprfetch = 2;
if(isset($xml) && isset($xml->channel)){
	foreach($xml->channel->item as $rssItem){
		$i++;
		if($i <= $fluxprfetch){
			
			$fpulldisplay.='<tr><td><a href="'.$rssItem->link.'" target="_blank">'.$rssItem->title.'</a></td></tr>';
		}
	}
} else {
	$fpulldisplay='<tr><td>There are no pull requests.</td></tr>';
}

// FluxCP Commit Data
$grab = file_get_contents('http://spriterepository.com/FluxCP/output.txt');
$fluxlcd = explode('-:-',$grab);


	$frepourl= 'https://github.com/Akkarinage/FluxCP';
	$freponame= 'FluxCP';
	$fcomauthor= $fluxlcd[0];
	$fcomlogin= 'https://github.com/'.$fluxlcd[0];
	$fcommessage= $fluxlcd[1];
	$fcomurl= $fluxlcd[2];
?>