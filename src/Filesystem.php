<?php

namespace RuzovySlon\Filesystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\MountManager;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class Filesystem
{

	/**
	 * @var MountManager
	 */
	protected $flysystem;

	/**
	 * @var Structure
	 */
	protected $structure;

	public function __construct(MountManager $flysystem, Structure $structure)
	{
		$this->flysystem = $flysystem;
		$this->structure = $structure;
	}

	/**
	 * Does structure contain node with path?
	 * @param string $path
	 * @return bool
	 */
	public function has($path)
	{
		$path = PathHelper::sanitize($path);

		return $this->structure->hasNode($path);
	}

	/**
	 * Query for files with custom SQL.
	 * @param QueryObject $queryObject
	 * @return Collection
	 */
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

	public function getUrl($path, $storage = NULL)
	{
		$path = PathHelper::sanitize($path);

		if (is_null($storage)) {
			$node = $this->structure->getNode($path);
			$storage = $node['storage'];
		}
		$pathWithStorage = $storage . ':/' . $path;

		$filesystem = $this->flysystem->getFilesystem($storage);
		$adapter = $filesystem->getAdapter();
		if ($adapter instanceof Adapters\IUrlAware) {
			return $adapter->getUrl($path);
		}
	}

	/**
	 * Read file contents.
	 * @param string $path
	 * @param string $storage Optimalization: Eg. Already known after query.
	 * @return string|false
	 */
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

	/**
	 * Create or update file.
	 * @param string $pathWithStorage Eg.: local://node/node/file.txt
	 * @param string $contents
	 * @return boolean
	 */
	public function put($pathWithStorage, $contents)
	{
		list($storage, $path) = explode('://', $pathWithStorage, 2);
		$path = PathHelper::sanitize($path);

		if ($this->has($path)) {
			$this->delete($path);
		}

		$status = $this->flysystem->put($pathWithStorage, $contents);

		if ($status) {
			$this->structure->insertNode($storage, $path);
			return $this->structure->hasNode($path);
		}

		return FALSE;
	}

	/**
	 * Delete file.
	 * @param string $path
	 * @param string $storage Optimalization: If already known. (Eg.: from query())
	 * @return boolean
	 */
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
			try {
				$status = $this->flysystem->delete($pathWithStorage);
			} catch (FileNotFoundException $ex) {
				$status = TRUE;
			}
		}

		if ($status) {
			$this->structure->deleteNode($path);
		}

		return $this->structure->hasNode($path);
	}

	/**
	 * Rename file.
	 * @param string $path
	 * @param string $newName Only the new name, NOT the whole path.
	 * @param string $storage
	 */
	public function rename($path, $newName, $storage = NULL)
	{
		$path = PathHelper::sanitize($path);

		if (is_null($storage)) {
			$node = $this->structure->getNode($path);
			$storage = $node['storage'];
		}
		$pathWithStorage = $storage . ':/' . $path;

		$renamedPath = PathHelper::rename($path, $newName);
		$renamedPathWithStorage = $storage . ':/' . $renamedPath;

		$status = $this->flysystem->rename($pathWithStorage, $renamedPath);
		if ($status) {
			$this->structure->changePath($path, $renamedPath);
		}

	}

}
