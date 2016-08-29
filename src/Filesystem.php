<?php

namespace RuzovySlon\Filesystem;

use Echo511\TreeTraversal\Tree;
use League\Flysystem\MountManager;

class Filesystem
{

	/**
	 *
	 * @var MountManager
	 */
	protected $flysystem;

	/**
	 *
	 * @var Structure
	 */
	protected $structure;

	function __construct(MountManager $flysystem, Structure $structure)
	{
		$this->flysystem = $flysystem;
		$this->structure = $structure;
	}

	public function copy($fromNodePath, $targetNodePath, $newNodeFilesystem = NULL, $newNodeName = NULL)
	{
		
	}

	public function delete($path)
	{
		
	}

	public function has($path)
	{
		$path = PathHelper::sanitize($path);

		return $this->structure->hasNode($path);
	}

	public function query(IQueryObject $queryObject)
	{
		
	}

	public function read($path)
	{
		$path = PathHelper::sanitize($path);

		$node = $this->structure->getNode($path);

		$pathWithStorage = $node['storage'] . ':/' . $path;
		return $this->flysystem->read($pathWithStorage);
	}

	public function rename($path, $name)
	{
		
	}

	public function url($path)
	{
		
	}

	public function put($pathWithStorage, $contents)
	{
		list($storage, $path) = explode('://', $pathWithStorage, 2);
		$path = PathHelper::sanitize($path);
		$status = $this->flysystem->put($pathWithStorage, $contents);

		if ($status) {
			$this->structure->insertNode($storage, $path);
			return $this->structure->hasNode($path);
		}

		return FALSE;
	}

}
