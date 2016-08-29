<?php

namespace RuzovySlon\Filesystem;

use FluentPDO;
use PDO;

class Database
{

	/**
	 *
	 * @var FluentPDO
	 */
	protected $fluentPdo;
	protected $table;

	public function __construct(PDO $pdo, $table)
	{
		$this->fluentPdo = new FluentPDO($pdo);
		$this->table = $table;
	}

	public function table()
	{
		return $this->fluentPdo->from($this->table);
	}

	public function selectNode()
	{
		return $this->table()
				->select(null)
				->select('id, lft, rgt, dpt, prt, storage, name, path');
	}

	public function update()
	{
		return $this->fluentPdo->update($this->table);
	}

}
