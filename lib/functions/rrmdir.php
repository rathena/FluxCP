<?php
function rrmdir($dir) {
	if (!is_dir($dir)) {
		return;
	}

	$dirIterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

	foreach ($iterator as $item) {
		$itemPath = $item->getPathname();

		if ($item->isDir() && !$item->isLink()) {
			@rmdir($itemPath);
		} else {
			@unlink($itemPath);
		}
	}

	@rmdir($dir);
}
?>
