<?php

namespace RuzovySlon\Filesystem;

use Echo511\TreeTraversal\Tree;
use FluentPDO;
use RuntimeException;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class Structure
{

	/**
	 *
	 * @var Database
	 */
	protected $database;

	/**
	 *
	 * @var Tree
	 */
	protected $tree;

	function __construct(Database $database, Tree $tree)
	{
		$this->database = $database;
		$this->tree = $tree;
	}

	/**
	 * Return node information.
	 * @param string $path
	 * @return array
	 * @throws RuntimeException
	 */
	public function getNode($path)
	{
		$node = $this->database->selectNode()
			->where('path', $path)
			->fetch();

		if (!$node) {
			throw new RuntimeException("Node $path could not be fetched!");
		}

		return $node;
	}

	/**
	 * Insert node under target and set storage designation.
	 * @param string $storage
	 * @param string $newNodeRelativePath
	 * @param string $targetNodePath
	 */
	public function insertNode($storage, $newNodeRelativePath, $targetNodePath = NULL)
	{
		// sanitize paths
		$targetNodePath = PathHelper::sanitize($targetNodePath);
		$newNodeRelativePath = PathHelper::sanitize($newNodeRelativePath);

		// explode tree
		$nodeTree = explode("/", trim($newNodeRelativePath, "/"));

		// pick first and shift the rest
		$nodeName = array_shift($nodeTree);

		// compute paths and hashes
		$newNodePath = PathHelper::sanitize($targetNodePath . '/' . $nodeName);
		$newNodePathHash = md5($newNodePath);
		$targetNodePathHash = md5($targetNodePath);

		// insert
		if (!$this->hasNode($newNodePath)) {
			$this->tree->insertNode($targetNodePathHash, $newNodePathHash, Tree::MODE_UNDER);
			$this->database->update()
				->set('path', $newNodePath)
				->set('storage', $storage)
				->where('hash', $newNodePathHash)
				->execute();
		}

		// recursion
		if (count($nodeTree) > 0) {
			$this->insertNode($storage, implode("/", $nodeTree), $newNodePath, Tree::MODE_UNDER);
		}
	}

	/**
	 * Delete node.
	 * @param string $path
	 */
	public function deleteNode($path)
	{
		$path = PathHelper::sanitize($path);
		$pathHash = md5($path);
		$this->tree->deleteNode($pathHash);
	}

	/**
	 * Does structure contain path?
	 * @param string $path
	 * @return bool
	 */
	public function hasNode($path)
	{
		$path = PathHelper::sanitize($path);
		$pathHash = md5($path);
		return $this->database->table()
				->where('hash', $pathHash)
				->count() > 0;
	}

	/**
	 * Forcefully updates path.
	 * Does not provide structure checks. Eg. tree structure will be intact.
	 * Use with caution!!
	 * @param type $path
	 * @param type $newPath
	 */
	public function changePath($path, $newPath)
	{
		$path = PathHelper::sanitize($path);
		$newPath = PathHelper::sanitize($newPath);
		$pathHash = md5($path);
		$newPathHash = md5($newPath);

		$this->database->update()
			->set([
			    'hash' => $newPathHash,
			    'path' => $newPath,
			])
			->where('hash = ?', $pathHash)
			->execute();
	}

	/**
	 * Get FluentPDO
	 * @return FluentPDO
	 */
	public function getFluent()
	{
		return $this->database->getFluent();
	}

}
