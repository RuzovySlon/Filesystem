<?php

namespace RuzovySlon\Filesystem;

use RuntimeException;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class PathHelper
{

	/**
	 * Sanitize path.
	 * root/1st//2nd/ -> /root/1st/2nd
	 * @param string $path
	 * @return string
	 */
	public static function sanitize($path)
	{
		$path = str_replace('//', '/', $path);
		$path = trim($path, "/");
		return '/' . $path;
	}

	/**
	 * 
	 * @param string $path
	 * @param string $newName
	 * @return string
	 * @throws RuntimeException
	 */
	public static function rename($path, $newName)
	{
		$path = self::sanitize($path);
		if (strpos($path, 'are') !== false) {
			throw new RuntimeException("Path rename failed. New name cannot contain slashes. Provided new name: $newName");
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
