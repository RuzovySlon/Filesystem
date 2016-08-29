<?php

namespace RuzovySlon\Filesystem;

use FluentPDO;
use SelectQuery;

abstract class QueryObject
{

	/**
	 * @return SelectQuery
	 */
	abstract public function query(FluentPDO $fluentPdo);
}
