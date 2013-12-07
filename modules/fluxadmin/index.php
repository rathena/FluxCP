<?php
/* FluxAdmin Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$grab = file_get_contents('http://spriterepository.com/FluxCP/output.txt');
$fluxlcd = explode('-:-',$grab);
//	$fpulldisplay=NULL;
//	if($flatestPull > 0){
		//$fpulldisplay = '';
		//$i=0;
		//while($i<$fnumberofpulls){
//			$epoch=strtotime($fpulls[$i]['created_at']);
		//	/$dt = new DateTime("@$epoch");
			//$/fpulldisplay.='<tr><td><a title="'.$fpulls[$i]['title'].'" href="'.$fpulls[$i]['html_url'].'" target="_blank">+ '.$fpulls[$i]['title'].'</a><br /><small>&nbsp;&nbsp;&nbsp;&nbsp;'.$dt->format('d M').'</td></tr>';
			//$i++;
		///}
		//$fpulldisplay.='</ul>';
	//} else { 
//		$fpulldisplay='<tr><td>There are no pull requests.</td></tr>';
//	}

		$frepourl= 'https://github.com/Akkarinage/FluxCP';
		$freponame= 'FluxCP';
		$fcomauthor= $fluxlcd[0];
		$fcomlogin= 'https://github.com/'.$fluxlcd[0];
		$fcommessage= $fluxlcd[1];
		$fcomurl= $fluxlcd[2];



?>