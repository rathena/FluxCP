<?php
/**
 * Get the repository version from the GIT hash or SVN revision
 *
 * @return int Revision number
 */
function getReposVersion()
{
	$gitDir = FLUX_ROOT.'/.git';

	if(is_dir($gitDir)) {
		return git_hash();
	} else {
		return null;
	}
}

/**
 * Get the GIT hash of a directory.
 *
 * @param string file name.
 * @return int GIT hash
 */
function git_hash() {
	$file = FLUX_ROOT.'/.git/refs/heads/master';
	if (file_exists($file) && is_readable($file)) {
		$lines = implode('', file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
		if(isset($lines)) {
			return trim(substr($lines, 0, 10));
		}
		return null;
	} else {
		return null;
	}
}

?>
