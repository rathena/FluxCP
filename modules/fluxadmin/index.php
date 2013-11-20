<?php
/* FluxAdmin Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

// Fetch FluxCP Github Data

function get_json($url){
	$base = "https://api.github.com/";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $base . $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$content = curl_exec($curl);
	curl_close($curl);
	return $content;
}

// Latest Commits
function fget_latest_repo($gituser) {
	$json = json_decode(get_json("users/$gituser/repos"),true);
	function fcompare_pushed_at($b, $a){
		return strnatcmp($a['pushed_at'], $b['pushed_at']);
	}
	usort($json, 'fcompare_pushed_at');
	$json = $json[0];
	return $json;
}
function get_commits($gituser,$gitrepo){
  return json_decode(get_json("repos/$gituser/$gitrepo/commits"),true);
}

// Pull Requests
function get_pulls($gituser,$gitrepo){
	return json_decode(get_json("repos/$gituser/$gitrepo/pulls"),true);
}

// FluxCP
$flatestRepo = fget_latest_repo("Akkarinage");
$fcommits = get_commits("Akkarinage","FluxCP");
$flatestCommit = $fcommits[0];
$fpulls = get_pulls("Akkarinage","FluxCP");
$fnumberofpulls = count($fpulls);
$flatestPull = $fpulls[0];

$frepoURL = $flatestRepo["html_url"];
$frepoName = $flatestRepo["name"];
$frepoDescription = $flatestRepo["description"];
$fgravatar = $flatestRepo["owner"]["avatar_url"];
$fauthor = $flatestCommit["commit"]["author"]["name"];
$flogin = $flatestCommit["author"]["login"];
$fuserURL = "https://github.com/$flogin";
$fcommitMessage = $flatestCommit["commit"]["message"];
$fcommitSHA = $flatestCommit["sha"];
$fcommitURL = "https://github.com/$flogin/$frepoName/commit/$fcommitSHA";

$fdisplaycommit='<tr><td>'.$fcommitMessage.'<br /><br />
Author: <a href="'.$fuserURL.'">'.$fauthor.'</a> | Repo Link: <a href="'.$fcommitURL.'">FluxCP</a></td></tr>';

$fpulldisplay=NULL;
if($flatestPull > 0){
	$fpulldisplay = '';
	$i=0;
	while($i<$fnumberofpulls){
		$epoch=strtotime($fpulls[$i]['created_at']);
		$dt = new DateTime("@$epoch");
		$fpulldisplay.='<tr><td><a title="'.$fpulls[$i]['title'].'" href="'.$fpulls[$i]['html_url'].'" target="_blank">+ '.$fpulls[$i]['title'].'</a><br /><small>&nbsp;&nbsp;&nbsp;&nbsp;'.$dt->format('d M').'</td></tr>';
		$i++;
	}
	$fpulldisplay.='</ul>';
} else { 
	$fpulldisplay='<tr><td>There are no pull requests.</td></tr>';
}

function rget_latest_repo($gituser) {
	$json = json_decode(get_json("users/$gituser/repos"),true);
	function rcompare_pushed_at($b, $a){
		return strnatcmp($a['pushed_at'], $b['pushed_at']);
	}
	usort($json, 'rcompare_pushed_at');
	$json = $json[0];
	return $json;
}

// rAthena
$rlatestRepo = rget_latest_repo("rathena");
$rcommits = get_commits("rathena","rathena");
$rlatestCommit = $rcommits[0];
$rpulls = get_pulls("rathena","rathena");
$rnumberofpulls = count($rpulls);
$rlatestPull = $rpulls[0];

$rrepoURL = $rlatestRepo["html_url"];
$rrepoName = $rlatestRepo["name"];
$rrepoDescription = $rlatestRepo["description"];
$rgravatar = $rlatestRepo["owner"]["avatar_url"];
$rauthor = $rlatestCommit["commit"]["author"]["name"];
$rlogin = $rlatestCommit["author"]["login"];
$ruserURL = "https://github.com/$rlogin";
$rcommitMessage = $rlatestCommit["commit"]["message"];
$rcommitSHA = $rlatestCommit["sha"];
$rcommitURL = "https://github.com/$rlogin/$rrepoName/commit/$rcommitSHA";

$rdisplaycommit='<tr><td>'.$rcommitMessage.'<br /><br />
Author: <a href="'.$ruserURL.'">'.$rauthor.'</a> | Repo Link: <a href="'.$rcommitURL.'">rAthena</a></td></tr>';

$rpulldisplay=NULL;
if($rlatestPull > 0){
	$rpulldisplay = '';
	$i=0;
	while($i<$rnumberofpulls){
		$epoch=strtotime($rpulls[$i]['created_at']);
		$dt = new DateTime("@$epoch");
		$rpulldisplay.='<tr><td><a title="'.$rpulls[$i]['title'].'" href="'.$rpulls[$i]['html_url'].'" target="_blank">+ '.$rpulls[$i]['title'].'</a><br /><small>&nbsp;&nbsp;&nbsp;&nbsp;'.$dt->format('d M').'</td></tr>';
		$i++;
	}
	$rpulldisplay.='</ul>';
} else { 
	$rpulldisplay='<tr><td>There are no pull requests.</td></tr>';
}

?>