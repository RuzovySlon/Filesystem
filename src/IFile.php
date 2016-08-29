<?php

namespace RuzovySlon\Filesystem;

interface IFile
{

	public function write($contents);

	public function read();

	public function delete();

	public function url();
}
