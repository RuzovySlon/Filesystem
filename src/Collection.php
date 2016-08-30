<?php

namespace RuzovySlon\Filesystem;

use ArrayObject;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class Collection extends ArrayObject
{

	/**
	 * Does collection contain path?
	 * @param string $path
	 * @return bool
	 */
	public function has($path)
	{
		$path = PathHelper::sanitize($path);
		return array_key_exists($path, $this);
	}

}
