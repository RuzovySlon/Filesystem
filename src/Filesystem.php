<?php

namespace RuzovySlon\Filesystem;

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

	public function delete($path, $storage = NULL)
	{
		$path = PathHelper::sanitize($path);

		if (is_null($storage)) {
			$node = $this->structure->getNode($path);
			$storage = $node['storage'];
		}
		$pathWithStorage = $storage . ':/' . $path;

		$status = $this->flysystem->deleteDir($pathWithStorage);
		if (!$status) {
			$status = $this->flysystem->delete($pathWithStorage);
		}

		if ($status) {
			$this->structure->deleteNode($path);
		}

		return FALSE;
	}

	public function has($path)
	{
		$path = PathHelper::sanitize($path);

		return $this->structure->hasNode($path);
	}

	public function query(QueryObject $queryObject)
	{
		$rows = $queryObject->fetchAll($this->structure->getFluent());

		$files = [];
		foreach ($rows as $path => $row) {
			$node = [
				'hash' => $row['hash'],
				'path' => $row['path'],
				'lft' => $row['lft'],
				'rgt' => $row['rgt'],
				'dpt' => $row['dpt'],
				'parent' => $row['parent'],
				'storage' => $row['storage'],
			];
			$files[$path] = new File($path, $node, $row, $this);
		}

		return new Collection($files);
	}

	public function read($path, $storage = NULL)
	{
		$path = PathHelper::sanitize($path);

		if (is_null($storage)) {
			$node = $this->structure->getNode($path);
			$storage = $node['storage'];
		}
		$pathWithStorage = $storage . ':/' . $path;
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
