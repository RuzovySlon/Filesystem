<?php

use Echo511\TreeTraversal\Tree;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Filesystem2;
use League\Flysystem\MountManager;
use RuzovySlon\Filesystem\Database;
use RuzovySlon\Filesystem\Filesystem;
use RuzovySlon\Filesystem\QueryObject;
use RuzovySlon\Filesystem\Structure;

class CollectionCest
{

	/**
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	public function _before(UnitTester $I)
	{
		$I->deleteDir($this->getFilesystemFilePath(''));
		$memoryAdapter = new Local($this->getFilesystemFilePath(''));
		$memoryFilesystem = new Filesystem2($memoryAdapter);
		$filesystems = [
			'local' => $memoryFilesystem,
		];
		$flysystem = new MountManager($filesystems);

		$pdo = new PDO('mysql:dbname=rsfilesystem;host=localhost', 'rsfilesystem');
		$table = 'filesystem';
		$database = new Database($pdo, $table);

		$config = [
			'table' => 'filesystem',
			'id' => 'hash',
			'prt' => 'parent',
		];
		$tree = new Tree($config, $pdo);
		$structure = new Structure($database, $tree);

		$this->filesystem = new Filesystem($flysystem, $structure);
	}

	public function _after(UnitTester $I)
	{
		
	}

	public function testFlow(UnitTester $I)
	{
		$this->filesystem->put('local://root/1st/2nd/3rd/file.txt', 'ANDROMEDA');
		$this->filesystem->put('local://root/1st/2nd/3rd/4th/file.txt', 'ORPHEUS');
		$this->filesystem->put('local://root/1st/2nd/3rd/4th/5th/file.txt', 'BELZEBUB');

		$collection = $this->filesystem->query(new MyQuery());
		$I->assertInstanceOf('RuzovySlon\Filesystem\Collection', $collection);
		$I->assertTrue($collection->has('/root/1st/2nd/3rd/file.txt'));
		$I->assertTrue($collection->has('/root/1st/2nd/3rd/4th/file.txt'));
		$I->assertTrue($collection->has('/root/1st/2nd/3rd/4th/5th/file.txt'));
		$I->assertFalse($collection->has('/root/1st/2nd/3rd/4th/5th/file.txtasd'));

		$I->assertEquals('/root/1st/2nd/3rd/file.txt', $collection['/root/1st/2nd/3rd/file.txt']->getPath());
		$I->assertEquals('ANDROMEDA', $collection['/root/1st/2nd/3rd/file.txt']->read());

		foreach ($collection as $path => $file) {
			$I->assertInstanceOf('\RuzovySlon\Filesystem\File', $file);
			$I->assertEquals($path, $file->getPath());
			$I->assertEquals($file->read(), $this->filesystem->read($file->getPath()));
		}
	}

	protected function getFilesystemFilePath($path)
	{
		return __DIR__ . '/../_output/filesystem' . $path;
	}

}

class MyQuery extends QueryObject
{

	public function query(\FluentPDO $fluentPdo)
	{
		return $fluentPdo->from('filesystem');
	}

}
