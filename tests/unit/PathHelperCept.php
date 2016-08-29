<?php

$I = new UnitTester($scenario);
$I->wantTo('test path sanitization and rename in path');

$expected = '/root/1st/2nd';
$actual = RuzovySlon\Filesystem\PathHelper::sanitize('root//1st/2nd///');
$I->assertEquals($expected, $actual);

$expected = '/root/newname.txt';
$actual = RuzovySlon\Filesystem\PathHelper::rename('/root/oldname.txt', 'newname.txt');
$I->assertEquals($expected, $actual);
