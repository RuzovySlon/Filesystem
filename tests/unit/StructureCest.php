<?php

use Echo511\TreeTraversal\Tree;
use RuzovySlon\Filesystem\Database;
use RuzovySlon\Filesystem\Structure;

class StructureCest
{

	/**
	 *
	 * @var Structure
	 */
	private $structure;

	/**
	 *
	 * @var FluentPDO
	 */
	private $fluentPdo;

	public function _before(UnitTester $I)
	{
		$pdo = new PDO('mysql:dbname=rsfilesystem;host=localhost', 'rsfilesystem');
		$table = 'filesystem';
		$database = new Database($pdo, $table);

		$config = [
			'table' => 'filesystem',
			'id' => 'hash',
			'prt' => 'parent',
		];
		$tree = new Tree($config, $pdo);

		$this->structure = new Structure($database, $tree);
		$this->fluentPdo = new FluentPDO($pdo);
	}

	public function _after(UnitTester $I)
	{
		
	}

	public function testInsertNodeInEmptyTree(UnitTester $I)
	{
		$this->structure->insertNode('local', '/root/1st/2nd/3rd/file.jpg');
		$expected = array(
			0 => array(
				'hash' => '887904812217cca9bc2b9adb875daf42',
				'path' => '/root',
				'lft' => '1',
				'rgt' => '10',
				'dpt' => '0',
				'parent' => null,
				'storage' => 'local',
			),
			1 => array(
				'hash' => '6e9d270a8fdacb7e219531c991031f18',
				'path' => '/root/1st',
				'lft' => '2',
				'rgt' => '9',
				'dpt' => '1',
				'parent' => '887904812217cca9bc2b9adb875daf42',
				'storage' => 'local',
			),
			2 => array(
				'hash' => '11e602a9898f62b450f6e9275c3bf3d3',
				'path' => '/root/1st/2nd',
				'lft' => '3',
				'rgt' => '8',
				'dpt' => '2',
				'parent' => '6e9d270a8fdacb7e219531c991031f18',
				'storage' => 'local',
			),
			3 => array(
				'hash' => '68c9e70656304d23be686a951d6a1cd2',
				'path' => '/root/1st/2nd/3rd',
				'lft' => '4',
				'rgt' => '7',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			4 => array(
				'hash' => '6af237b59bf1bd4e6cf2687fd73b5a4c',
				'path' => '/root/1st/2nd/3rd/file.jpg',
				'lft' => '5',
				'rgt' => '6',
				'dpt' => '4',
				'parent' => '68c9e70656304d23be686a951d6a1cd2',
				'storage' => 'local',
			),
		);
		$I->assertEquals($expected, $this->dumpStructure());

		$this->structure->insertNode('local', '/root/1st/2nd/_3rd/file.jpg');
		$expected = array(
			0 => array(
				'hash' => '887904812217cca9bc2b9adb875daf42',
				'path' => '/root',
				'lft' => '1',
				'rgt' => '14',
				'dpt' => '0',
				'parent' => null,
				'storage' => 'local',
			),
			1 => array(
				'hash' => '6e9d270a8fdacb7e219531c991031f18',
				'path' => '/root/1st',
				'lft' => '2',
				'rgt' => '13',
				'dpt' => '1',
				'parent' => '887904812217cca9bc2b9adb875daf42',
				'storage' => 'local',
			),
			2 => array(
				'hash' => '11e602a9898f62b450f6e9275c3bf3d3',
				'path' => '/root/1st/2nd',
				'lft' => '3',
				'rgt' => '12',
				'dpt' => '2',
				'parent' => '6e9d270a8fdacb7e219531c991031f18',
				'storage' => 'local',
			),
			3 => array(
				'hash' => '68c9e70656304d23be686a951d6a1cd2',
				'path' => '/root/1st/2nd/3rd',
				'lft' => '4',
				'rgt' => '7',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			4 => array(
				'hash' => '6af237b59bf1bd4e6cf2687fd73b5a4c',
				'path' => '/root/1st/2nd/3rd/file.jpg',
				'lft' => '5',
				'rgt' => '6',
				'dpt' => '4',
				'parent' => '68c9e70656304d23be686a951d6a1cd2',
				'storage' => 'local',
			),
			5 => array(
				'hash' => '460666d2f6f603928d13aaaf580663a9',
				'path' => '/root/1st/2nd/_3rd',
				'lft' => '8',
				'rgt' => '11',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			6 => array(
				'hash' => 'ec844743b44d6b1aae0b43e9eb8b0c9f',
				'path' => '/root/1st/2nd/_3rd/file.jpg',
				'lft' => '9',
				'rgt' => '10',
				'dpt' => '4',
				'parent' => '460666d2f6f603928d13aaaf580663a9',
				'storage' => 'local',
			),
		);
		$I->assertEquals($expected, $this->dumpStructure());
	}

	public function testInsertNodeRelativeToOther(UnitTester $I)
	{
		$this->insertDummy();
		$this->structure->insertNode('local', '/_3rd/file.jpg', '/root/1st/2nd/');
		$expected = array(
			0 => array(
				'hash' => '887904812217cca9bc2b9adb875daf42',
				'path' => '/root',
				'lft' => '1',
				'rgt' => '14',
				'dpt' => '0',
				'parent' => null,
				'storage' => 'local',
			),
			1 => array(
				'hash' => '6e9d270a8fdacb7e219531c991031f18',
				'path' => '/root/1st',
				'lft' => '2',
				'rgt' => '13',
				'dpt' => '1',
				'parent' => '887904812217cca9bc2b9adb875daf42',
				'storage' => 'local',
			),
			2 => array(
				'hash' => '11e602a9898f62b450f6e9275c3bf3d3',
				'path' => '/root/1st/2nd',
				'lft' => '3',
				'rgt' => '12',
				'dpt' => '2',
				'parent' => '6e9d270a8fdacb7e219531c991031f18',
				'storage' => 'local',
			),
			3 => array(
				'hash' => '68c9e70656304d23be686a951d6a1cd2',
				'path' => '/root/1st/2nd/3rd',
				'lft' => '4',
				'rgt' => '7',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			4 => array(
				'hash' => '6af237b59bf1bd4e6cf2687fd73b5a4c',
				'path' => '/root/1st/2nd/3rd/file.jpg',
				'lft' => '5',
				'rgt' => '6',
				'dpt' => '4',
				'parent' => '68c9e70656304d23be686a951d6a1cd2',
				'storage' => 'local',
			),
			5 => array(
				'hash' => '460666d2f6f603928d13aaaf580663a9',
				'path' => '/root/1st/2nd/_3rd',
				'lft' => '8',
				'rgt' => '11',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			6 => array(
				'hash' => 'ec844743b44d6b1aae0b43e9eb8b0c9f',
				'path' => '/root/1st/2nd/_3rd/file.jpg',
				'lft' => '9',
				'rgt' => '10',
				'dpt' => '4',
				'parent' => '460666d2f6f603928d13aaaf580663a9',
				'storage' => 'local',
			),
		);
		$I->assertEquals($expected, $this->dumpStructure());
	}

	public function testHasNode(UnitTester $I)
	{
		$this->insertDummy();
		$I->assertFalse($this->structure->hasNode('/asd'));
		$I->assertTrue($this->structure->hasNode('root/'));
		$I->assertTrue($this->structure->hasNode('/root'));
		$I->assertTrue($this->structure->hasNode('/root/'));
	}

	public function testDeleteNode(UnitTester $I)
	{
		$this->insertDummy();
		$this->structure->deleteNode('/root/1st/2nd');
		$expected = array(
			0 => array(
				'hash' => '887904812217cca9bc2b9adb875daf42',
				'path' => '/root',
				'lft' => '1',
				'rgt' => '4',
				'dpt' => '0',
				'parent' => null,
				'storage' => 'local',
			),
			1 => array(
				'hash' => '6e9d270a8fdacb7e219531c991031f18',
				'path' => '/root/1st',
				'lft' => '2',
				'rgt' => '3',
				'dpt' => '1',
				'parent' => '887904812217cca9bc2b9adb875daf42',
				'storage' => 'local',
			),
		);
		$I->assertEquals($expected, $this->dumpStructure());
	}

	protected function dumpStructure()
	{
		return $this->fluentPdo->from('filesystem')->orderBy('lft')->fetchAll();
	}

	protected function insertDummy()
	{
		$values = array(
			0 => array(
				'hash' => '887904812217cca9bc2b9adb875daf42',
				'path' => '/root',
				'lft' => '1',
				'rgt' => '10',
				'dpt' => '0',
				'parent' => null,
				'storage' => 'local',
			),
			1 => array(
				'hash' => '6e9d270a8fdacb7e219531c991031f18',
				'path' => '/root/1st',
				'lft' => '2',
				'rgt' => '9',
				'dpt' => '1',
				'parent' => '887904812217cca9bc2b9adb875daf42',
				'storage' => 'local',
			),
			2 => array(
				'hash' => '11e602a9898f62b450f6e9275c3bf3d3',
				'path' => '/root/1st/2nd',
				'lft' => '3',
				'rgt' => '8',
				'dpt' => '2',
				'parent' => '6e9d270a8fdacb7e219531c991031f18',
				'storage' => 'local',
			),
			3 => array(
				'hash' => '68c9e70656304d23be686a951d6a1cd2',
				'path' => '/root/1st/2nd/3rd',
				'lft' => '4',
				'rgt' => '7',
				'dpt' => '3',
				'parent' => '11e602a9898f62b450f6e9275c3bf3d3',
				'storage' => 'local',
			),
			4 => array(
				'hash' => '6af237b59bf1bd4e6cf2687fd73b5a4c',
				'path' => '/root/1st/2nd/3rd/file.jpg',
				'lft' => '5',
				'rgt' => '6',
				'dpt' => '4',
				'parent' => '68c9e70656304d23be686a951d6a1cd2',
				'storage' => 'local',
			),
		);
		$this->fluentPdo->insertInto('filesystem', $values)->execute();
	}

}
