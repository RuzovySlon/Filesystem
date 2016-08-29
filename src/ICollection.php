<?php

namespace RuzovySlon\Filesystem;

interface ICollection extends \Iterator
{

	public function has($path);
}
