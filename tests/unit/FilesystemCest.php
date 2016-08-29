<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Filesystem2;
use League\Flysystem\MountManager;
use RuzovySlon\Filesystem\Filesystem;

class FilesystemCest
{

	/**
	 *
	 * @var MountManager
	 */
	private $flysystem;

	public function _before(UnitTester $I)
	{
		$I->deleteDir($this->getFilesystemFilePath(''));
		$memoryAdapter = new Local($this->getFilesystemFilePath(''));
		$memoryFilesystem = new Filesystem2($memoryAdapter);
		$filesystems = [
			'local' => $memoryFilesystem,
		];
		$this->flysystem = new MountManager($filesystems);
	}

	public function _after(UnitTester $I)
	{
		$I->deleteDir($this->getFilesystemFilePath(''));
	}

	public function testPut(UnitTester $I)
	{
		$structure = Mockery::mock('\RuzovySlon\Filesystem\Structure')
			->shouldReceive('insertNode')
			->once()
			->with('local', '/root/1st/file.txt')
			->andReturn(TRUE)
			->shouldReceive('hasNode')
			->once()
			->with('/root/1st/file.txt')
			->andReturn(TRUE)
			->getMock();
		$filesystem = new Filesystem($this->flysystem, $structure);
		$filesystem->put('local://root/1st/file.txt', 'ANDROMEDA');
		$I->seeFileFound($this->getFilesystemFilePath('/root/1st/file.txt'));
	}

	public function testHas(UnitTester $I)
	{
		$structure = Mockery::mock('\RuzovySlon\Filesystem\Structure')
			->shouldReceive('hasNode')
			->once()
			->with('/root')
			->andReturn(TRUE)
			->getMock();
		$filesystem = new Filesystem($this->flysystem, $structure);
		$has = $filesystem->has('/root');
		$I->assertTrue($has);

		$structure = Mockery::mock('\RuzovySlon\Filesystem\Structure')
			->shouldReceive('hasNode')
			->once()
			->with('/root')
			->andReturn(FALSE)
			->getMock();
		$filesystem = new Filesystem($this->flysystem, $structure);
		$has = $filesystem->has('/root');
		$I->assertFalse($has);
	}

	protected function getFilesystemFilePath($path)
	{
		return __DIR__ . '/../_output/filesystem' . $path;
	}

}
