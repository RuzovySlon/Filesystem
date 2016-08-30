<?php

namespace RuzovySlon\Filesystem;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class File
{

	/**
	 * File path in tree structure.
	 * @var string
	 */
	private $path;

	/**
	 * File node info. Eg. columns of mysql table in default state as in test dump.
	 * @var array
	 */
	private $node;

	/**
	 * Full row fetched from database. Eg. the node info and other (inner join perhaps)
	 * @var array
	 */
	private $row;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	function __construct($path, $node, $row, Filesystem $filesystem)
	{
		$this->path = PathHelper::sanitize($path);
		$this->node = $node;
		$this->row = $row;
		$this->filesystem = $filesystem;
	}

	/**
	 * Sanitized path. Eg. PathHelper::sanitize()
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Update file.
	 * @param string $contents
	 */
	public function put($contents)
	{
		$pathWithStorage = $this->node['storage'] . ':/' . $this->path;
		$this->filesystem->put($pathWithStorage, $contents);
	}

	/**
	 * Read file.
	 * @return string|false
	 */
	public function read()
	{
		return $this->filesystem->read($this->path, $this->node['storage']);
	}

	/**
	 * Delete file.
	 */
	public function delete()
	{
		$this->filesystem->delete($this->path, $this->node['storage']);
	}

	/**
	 * Return full row fetched from database.
	 * @return array
	 */
	public function getRow()
	{
		return $this->row;
	}

}
