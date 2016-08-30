<?php

namespace RuzovySlon\Filesystem;

use ArrayObject;

class Collection extends ArrayObject
{

	public function __construct($files)
	{
		parent::__construct($files);
	}

	public function has($path)
	{
		$path = PathHelper::sanitize($path);
		return array_key_exists($path, $this);
	}

}
