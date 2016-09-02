<?php

namespace RuzovySlon\Filesystem\Adapters;

use League\Flysystem\Adapter\Local as FlysystemLocal;

class Local extends FlysystemLocal implements IUrlAware
{

	protected $wwwRoot;


	public function setWwwRoot($wwwRoot)
	{
		$this->wwwRoot = rtrim($wwwRoot, "/");
	}


	public function getUrl($path)
	{
		return $this->wwwRoot . $path;
	}


}
