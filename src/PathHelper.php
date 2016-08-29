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
}