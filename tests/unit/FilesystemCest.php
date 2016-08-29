<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Filesystem2;
use League\Flysystem\MountManager;
use RuzovySlon\Filesystem\Database;
use RuzovySlon\Filesystem\Filesystem;
use RuzovySlon\Filesystem\Structure;

class FilesystemCest
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
		$tree = new Echo511\TreeTraversal\Tree($config, $pdo);
		$structure = new Structure($database, $tree);

		$this->filesystem = new Filesystem($flysystem, $structure);
	}

	public function _after(UnitTester $I)
	{
		$I->deleteDir($this->getFilesystemFilePath(''));
	}

	public function testPut(UnitTester $I)
	{
		$this->filesystem->put('local://root/1st/file.txt', 'ANDROMEDA');
		$I->seeFileFound($this->getFilesystemFilePath('/root/1st/file.txt'));
	}

	public function testHas(UnitTester $I)
	{
		$this->filesystem->put('local://root/1st/file.txt', 'ANDROMEDA');
		$has = $this->filesystem->has('/root');
		$I->assertTrue($has);

		$has = $this->filesystem->has('/roots');
		$I->assertFalse($has);
	}

	public function testRead(UnitTester $I)
	{
		$this->filesystem->put('local://root/1st/file.txt', 'ANDROMEDA');
		$contents = $this->filesystem->read('/root/1st/file.txt');
		$I->assertEquals('ANDROMEDA', $contents);
	}

	public function testDelete(UnitTester $I)
	{
		$this->filesystem->put('local://root/1st/file.txt', 'ANDROMEDA');
		$this->filesystem->put('local://root/1st/under/file.txt', 'ORPHEUS');
		$this->filesystem->put('local://root/1st/under/underworld/file.txt', 'BELZEBUB');
		$I->seeFileFound($this->getFilesystemFilePath('/root/1st/file.txt'));
		$I->seeFileFound($this->getFilesystemFilePath('/root/1st/under/file.txt'));
		$I->seeFileFound($this->getFilesystemFilePath('/root/1st/under/underworld/file.txt'));
		$I->assertTrue($this->filesystem->has('/root/1st/file.txt'));
		$I->assertTrue($this->filesystem->has('/root/1st/under/file.txt'));
		$I->assertTrue($this->filesystem->has('/root/1st/under/underworld/file.txt'));

		// delete belzebub
		$this->filesystem->delete('/root/1st/under/underworld/file.txt');
		$I->cantSeeFileFound($this->getFilesystemFilePath('/root/1st/under/underworld/file.txt'));
		$I->assertFalse($this->filesystem->has('/root/1st/under/underworld/file.txt'));

		// delete orpheus via parent directory
		$this->filesystem->delete('/root/1st/under');
		$I->cantSeeFileFound($this->getFilesystemFilePath('/root/1st/under'));
		$I->cantSeeFileFound($this->getFilesystemFilePath('/root/1st/under/file.txt'));
		$I->assertFalse($this->filesystem->has('/root/1st/under/file.txt'));

		// delete root
		$this->filesystem->delete('/root');
		$I->cantSeeFileFound($this->getFilesystemFilePath('/root'));
		$I->assertFalse($this->filesystem->has('/root'));
	}

	protected function getFilesystemFilePath($path)
	{
		return __DIR__ . '/../_output/filesystem' . $path;
	}

}
