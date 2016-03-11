<?php
class PathUtil
{
	static function directoryToArray($directory, $filter = null, $recursive = false, $listDirectories = true)
	{
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($directory. "/" . $file)) {
						if($recursive) {
							$array_items = array_merge($array_items, self::directoryToArray($directory. "/" . $file, $filter, $recursive, $listDirectories));
						}
						if($listDirectories)
						{
							$file = $directory . "/" . $file;
							$array_items[] = preg_replace("/\/\//si", "/", $file);
						}
					} else {
						$file = $directory . "/" . $file;
						if($filter == null || $filter == pathinfo($file, PATHINFO_EXTENSION))
						{
							$array_items[] = preg_replace("/\/\//si", "/", $file);
						}
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	}
}
?>
