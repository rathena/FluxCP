<?php
/**
 * Get the repository version from the GIT hash or SVN revision
 *
 * @return int Revision number
 */
function getReposVersion()
{
	$gitDir = FLUX_ROOT.'/.git';
	$svnDir = FLUX_ROOT.'/.svn';
	
	if (is_dir($gitDir)) {
		return git_hash();
	}
	else if (is_dir($svnDir)) {
		return svn_version();
	}
}

/**
 * Get the GIT hash of a directory.
 *
 * @param string file name.
 * @return int GIT hash
 */
function git_hash()
{
	$file = FLUX_ROOT.'/.git/refs/heads/master';
	
	if (file_exists($file) && is_readable($file)) {
		$lines = implode('', file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
		
		if(isset($lines)) {
			return trim(substr($lines, 0, 10));
		}
		return null;
	}
}

/**
 * Get the SVN revision of a directory.
 *
 * @param string `entries' file name.
 * @return int Revision number
 */
function svn_version()
{
	$rev = null;
	
	// Subversion 1.6 and lower
	$file = FLUX_ROOT.'/.svn/entries';
	if (file_exists($file) && is_readable($file)) {
		$fp  = fopen($file, 'r');
		$arr = explode("\n", fread($fp, 256));

		if (isset($arr[3]) && ctype_digit($found = trim($arr[3]))) {
			$rev = $found;
		}
		fclose($fp);
	}
	
	//Subversion 1.7 and up
	if(!isset($rev)) {
		$file = FLUX_ROOT.'/.svn/wc.db';
		$curr = 0;
		
		if (file_exists($file) && is_readable($file)) {
			$fp  = fopen($file, 'r');
			
			while(($line = fread($fp, 64))) {
				if(strstr($line,"!svn/ver/") && sscanf(strstr($line,"!svn/ver/"),"!svn/ver/%d/%*s", $curr) == 1) {
					if($curr > $rev) {
						$rev = $curr;
					}
				}
			}
			
			fclose($fp);
		}
	}
	
	return $rev;	
}

?>
