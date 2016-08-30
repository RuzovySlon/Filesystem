<?php

namespace RuzovySlon\Filesystem;

use FluentPDO;
use SelectQuery;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
abstract class QueryObject
{

	/**
	 * 
	 * @param FluentPDO $fluentPdo
	 * @return array [path => [row]]
	 */
	public function fetchAll(FluentPDO $fluentPdo)
	{
		return $this->query($fluentPdo)->fetchAll('path');
	}

	/**
	 * @return SelectQuery
	 */
	abstract protected function query(FluentPDO $fluentPdo);
}
