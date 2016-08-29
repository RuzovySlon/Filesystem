<?php

namespace RuzovySlon\Filesystem;

class File
{

	/**
	 *
	 * @var string
	 */
	private $path;

	/**
	 *
	 * @var array
	 */
	private $node;

	/**
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	function __construct($path, $node, Filesystem $filesystem)
	{
		$this->path = PathHelper::sanitize($path);
		$this->node = $node;
		$this->filesystem = $filesystem;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function put($contents)
	{
		$pathWithStorage = $this->node['storage'] . ':/' . $this->path;
		$this->filesystem->put($pathWithStorage, $contents);
	}

	public function read()
	{
		return $this->filesystem->read($this->path, $this->node['storage']);
	}

	public function delete()
	{
		$this->filesystem->delete($this->path, $this->node['storage']);
	}

}
