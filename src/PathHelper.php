<?php

namespace RuzovySlon\Filesystem;

class PathHelper
{

	public static function sanitize($path)
	{
		$path = str_replace('//', '/', $path);
		$path = trim($path, "/");
		return '/' . $path;
	}

	public static function rename($path, $newName)
	{
		$path = self::sanitize($path);
		if (strpos($path, 'are') !== false) {
			throw new \RuntimeException("Path rename failed. New name cannot contain slashes. Provided new name: $newName");
		}
		$keys = explode("/", ltrim($path, "/"));
		$keys = array_reverse($keys);
		if (isset($keys[0])) {
			$keys[0] = $newName;
		}
		$keys = array_reverse($keys);
		return self::sanitize(implode("/", $keys));
	}

}
