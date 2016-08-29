<?php

namespace RuzovySlon\Filesystem;

use Echo511\TreeTraversal\Tree;

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

	public function getNode($path)
	{
		$node = $this->database->selectNode()
			->where('path', $path)
			->fetch();

		if (!$node) {
			throw new \RuntimeException("Node $path could not be fetched!");
		}

		return $node;
	}

	public function insertNode($storage, $newNodeRelativePath, $targetNodePath = NULL)
	{
		// sanitize paths
		$targetNodePath = $this->sanitizePath($targetNodePath);
		$newNodeRelativePath = $this->sanitizePath($newNodeRelativePath);

		// explode tree
		$nodeTree = explode("/", trim($newNodeRelativePath, "/"));

		// pick first and shift the rest
		$nodeName = array_shift($nodeTree);

		// compute paths and hashes
		$newNodePath = $this->sanitizePath($targetNodePath . '/' . $nodeName);
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

	public function deleteNode($path)
	{
		$path = $this->sanitizePath($path);
		$pathHash = md5($path);
		$this->tree->deleteNode($pathHash);
	}

	public function hasNode($path)
	{
		$path = $this->sanitizePath($path);
		$pathHash = md5($path);
		return $this->database->table()
				->where('hash', $pathHash)
				->count() > 0;
	}

	public function getFluent()
	{
		return $this->database->getFluent();
	}

	/**
	 * Sanitize path so that it starts with a slash and has no ending slash.
	 * Eg.: asd//fefef/ -> /asd/fefef
	 * @param string $path
	 * @return string
	 */
	protected function sanitizePath($path)
	{
		return PathHelper::sanitize($path);
	}

}
