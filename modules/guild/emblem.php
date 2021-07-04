<?php
if (!defined('FLUX_ROOT')) exit;

function flux_get_default_bmp_data()
{
	$filename = sprintf('%s/emblem/%s', FLUX_DATA_DIR, Flux::config('MissingEmblemBMP'));
	if (file_exists($filename)) {
		return file_get_contents($filename);
	}
}

function flux_display_empty_emblem()
{
	$data = flux_get_default_bmp_data();
	header("Content-Type: image/bmp");
	header('Content-Length: '.strlen($data));
	echo $data;
	exit;
}

if (Flux::config('ForceEmptyEmblem'))
	flux_display_empty_emblem();

$serverName       = $params->get('login');
$athenaServerName = $params->get('charmap');
$guildID          = intval($params->get('id'));
$athenaServer     = Flux::getAthenaServerByName($serverName, $athenaServerName);

if (!$athenaServer || $guildID < 0)
	flux_display_empty_emblem();
else {
	if ($interval=Flux::config('EmblemCacheInterval')) {
		$interval *= 60;
		$dirname   = FLUX_DATA_DIR."/tmp/emblems/$serverName/$athenaServerName";
		$filename  = "$dirname/$guildID.png";
		
		if (!is_dir($dirname))
			if (Flux::config('RequireOwnership'))
				mkdir($dirname, 0700, true);
			else
				mkdir($dirname, 0777, true);
		elseif (file_exists($filename) && (time() - filemtime($filename)) < $interval) {
			header("Content-Type: image/png");
			header('Content-Length: '.filesize($filename));
			@readfile($filename);
			exit;
		}
	}
	
	if(Flux::config('EmblemUseWebservice')) {
		$db  = $athenaServer->charMapDatabase;
		$sql = "SELECT file_type, file_data FROM $db.guild_emblems WHERE guild_id = ? LIMIT 1";
		$sth = $athenaServer->connection->getStatement($sql);
		$sth->execute(array($guildID));
		$res = $sth->fetch();
		
		if (!$res->file_data)
			flux_display_empty_emblem();
		else {
			$image = imagecreatefromstring($res->file_data);
			$rgb =  imagecolorexact ($image, 255,0,255);
			imagecolortransparent($image, $rgb);
			
			header("Content-Type: image/png");
			
			if ($interval)
				imagepng($image, $filename);
			
			imagepng($image);
			exit;
		}
	} else {
		$db  = $athenaServer->charMapDatabase;
		$sql = "SELECT emblem_len, emblem_data FROM $db.guild WHERE guild_id = ? LIMIT 1";
		$sth = $athenaServer->connection->getStatement($sql);
		$sth->execute(array($guildID));
		$res = $sth->fetch();
		
		if (!$res || !$res->emblem_len)
			flux_display_empty_emblem();
		else {
			require_once 'functions/imagecreatefrombmpstring.php';
			
			$data  = @gzuncompress(pack('H*', $res->emblem_data));
			$image = imagecreatefrombmpstring($data);
			
			header("Content-Type: image/png");
			
			if ($interval)
				imagepng($image, $filename);
			
			imagepng($image);
			exit;
		}
	}
}
?>
