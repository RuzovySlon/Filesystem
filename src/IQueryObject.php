<?php

namespace RuzovySlon\Filesystem;

interface IQueryObject
{

	/**
	 * @return array [path => [id => ..., lft, rgt, dpt, prt, storage, name]]
	 */
	public function doQuery();
}
