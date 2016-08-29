<?php

namespace RuzovySlon\Filesystem;

interface IFilesystem
{

	public function has($path);

	public function read($path);

	public function write($path);

	public function rename($path, $name);

	public function copy($from, $to);

	public function delete($path);

	/**
	 * Return URL or NULL.
	 * @param string|NULL $path
	 */
	public function url($path);

	/**
	 * 
	 * @param IQueryObject $queryObject
	 * @return ICollection
	 */
	public function query(IQueryObject $queryObject);
}
