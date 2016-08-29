<?php

namespace RuzovySlon\Filesystem;

use ArrayObject;

class Collection extends ArrayObject
{

	/**
	 *
	 * @var File[]
	 */
	protected $files = [];

	function __construct($files)
	{
		$items = [];
		foreach ($files as $file) {
			$items[$file->getPath()] = $file;
		}
		parent::__construct($items);
	}

	public function has($path)
	{
		$path = PathHelper::sanitize($path);
		return array_key_exists($path, $this);
	}

}
