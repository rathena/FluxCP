<?php
require_once 'functions/imagecreatefrombmpstring.php';

/**
 *
 */
class Flux_EmblemExporter {
	/**
	 *
	 */
	public $loginAthenaGroup;
	
	/**
	 *
	 */
	public $athenaServers = array();
	
	/**
	 *
	 */
	public function __construct(Flux_LoginAthenaGroup $loginAthenaGroup)
	{
		$this->loginAthenaGroup = $loginAthenaGroup;
	}
	
	/**
	 *
	 */
	public function addAthenaServer(Flux_Athena $athenaServer)
	{
		if (!in_array($athenaServer, $this->loginAthenaGroup->athenaServers, true)) {
			throw new Flux_Error(
				"{$athenaServer->serverName} is not a valid char/map server defined in the {$this->loginAthenaGroup->serverName} group.");
		}
		
		$this->athenaServers[$athenaServer->serverName] = $athenaServer;
	}
	
	/**
	 *
	 */
	public function exportArchive()
	{
		$topDir  = $this->sanitizePathName($this->loginAthenaGroup->serverName);
		$tmpDir  = FLUX_DATA_DIR.'/tmp';
		$tmpFile = tempnam($tmpDir, 'zip');
		
		// Create zip archive.
		$zip = new ZipArchive();
		$zip->open($tmpFile, ZIPARCHIVE::OVERWRITE);
		$zip->addEmptyDir($topDir);
		
		foreach ($this->athenaServers as $athenaServer) {
			$athenaDir = $this->sanitizePathName($athenaServer->serverName);
			$zip->addEmptyDir("$topDir/$athenaDir");
			
			$sql = "SELECT name, emblem_data FROM {$athenaServer->charMapDatabase}.guild WHERE emblem_len > 0 ORDER BY name ASC";
			$sth = $athenaServer->connection->getStatement($sql);
			$sth->execute();
			
			$guilds = $sth->fetchAll();
			if ($guilds) {
				foreach ($guilds as $guild) {
					$emblemData  = @gzuncompress(pack('H*', $guild->emblem_data));
					$emblemImage = imagecreatefrombmpstring($emblemData);
					
					ob_start();
					imagepng($emblemImage);
					$data = ob_get_clean();
					
					$emblemName = sprintf('%s.png', $this->sanitizePathName($guild->name)); 
					$zip->addFromString("$topDir/$athenaDir/$emblemName", $data);
				}
			}
		}
		
		// Close archive.
		$zip->close();
		
		// Send out appropriate HTTP headers.
		$filename = urlencode(sprintf('%s-%s-emblems.zip', strtolower($topDir), date('Ymd')));
		header('Content-Type: application/zip');
		header('Content-Length: '.filesize($tmpFile));
		header("Content-Disposition: attachment; filename=$filename");
		
		// Read contents of the file.
		readfile($tmpFile);
		
		// Remove temporary file.
		unlink($tmpFile);
		exit;
	}
	
	/**
	 *
	 */
	private function sanitizePathName($pathName)
	{
		return preg_replace('/[^\w\d ]+/', '', $pathName);
	}
}
?>
