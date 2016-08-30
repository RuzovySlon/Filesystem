<?php

namespace RuzovySlon\Filesystem;

use FluentPDO;
use PDO;
use SelectQuery;
use UpdateQuery;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class Database
{

	/**
	 * @var FluentPDO
	 */
	protected $fluentPdo;

	/**
	 * MySQL table name.
	 * @var string
	 */
	protected $table;

	public function __construct(PDO $pdo, $table)
	{
		$this->fluentPdo = new FluentPDO($pdo);
		$this->table = $table;
	}

	/**
	 * @return FluentPDO
	 */
	public function getFluent()
	{
		return $this->fluentPdo;
	}

	/**
	 * Return selection query with table setted.
	 * @return SelectQuery
	 */
	public function table()
	{
		return $this->fluentPdo->from($this->table);
	}

	/**
	 * Return selection query with table and select prepared.
	 * @return SelectionQuery
	 */
	public function selectNode()
	{
		return $this->table()
				->select(null)
				->select('hash, path, lft, rgt, dpt, parent, storage');
	}

	/**
	 * Return update query with table setted.
	 * @return UpdateQuery
	 */
	public function update()
	{
		return $this->fluentPdo->update($this->table);
	}

}
