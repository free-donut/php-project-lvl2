<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Diff\getDiff;
use function GenDiff\Diff\getArray;
use function GenDiff\Builder\buildAST;
use function GenDiff\Parse\parser;
use function GenDiff\Plain\getPlain;
use function GenDiff\Render\render;

class diffTest extends TestCase
{

	public function testGetDiff()
	{
		$equals = "{\n    host: hexlet.io\n  + timeout: 20\n  - timeout: 50\n  - proxy: 123.234.53.22\n  + verbose: true\n}\n";
		$actual = getDiff('tests/testFiles/before.json', 'tests/testFiles/after.json', "json");
		$this->assertEquals($equals, $actual);

		$equals2 = "{\n    common: {\n        setting1: Value 1\n      - setting2: 200\n        setting3: true\n      - setting6: {\n            key: value\n        }\n      + setting4: blah blah\n      + setting5: {\n            key5: value5\n        }\n    }\n    group1: {\n      + baz: bars\n      - baz: bas\n        foo: bar\n    }\n  - group2: {\n        abc: 12345\n    }\n  + group3: {\n        fee: 100500\n    }\n}\n";
		$actual2 = getDiff('tests/testFiles/before1.json', 'tests/testFiles/after1.json', 'json');
		$this->assertEquals($equals2, $actual2);
	}

	public function testParser()
	{
		$equals = ["host" => "hexlet.io", "timeout" => 50, "proxy" => "123.234.53.22"];
		$filePath = 'tests/testFiles/before.yml';
		$actual = parser($filePath);
		$this->assertEquals($equals, $actual);

		$equals2 = [
			"common" => ["setting1" => "Value 1", "setting2" => 200, "setting3" => 1, "setting6" =>["key" => "value"]],
			"group1" => ["baz" => "bas", "foo" => "bar"],
			"group2" => ["abc" => 12345]
		];
		$filePath2 = 'tests/testFiles/before1.json';
		$actual2 = parser($filePath2);
		$this->assertEquals($equals2, $actual2);
	}

	public function testBuildAST()
	{
		$equals = [
			['type' => 'unchanged', 'key' => 'host', 'beforeValue' => 'hexlet.io', 'afterValue' => 'hexlet.io', 'child' => null],
			['type' => 'changed', 'key' => 'timeout', 'beforeValue' => '50', 'afterValue' => '20', 'child' => null],
			['type' => 'deleted', 'key' => 'proxy', 'beforeValue' => '123.234.53.22','afterValue' => null, 'child' => null],
			['type' => 'added', 'key' => 'verbose', 'beforeValue' => null,'afterValue' => true, 'child' => null]
		];
		$beforeData = parser('tests/testFiles/before.json');
		$afterData = parser('tests/testFiles/after.json');
		$actual = buildAST($beforeData, $afterData);
		$this->assertEquals($equals, $actual);

		$beforeData2 = parser('tests/testFiles/before.yml');
		$afterData2 = parser('tests/testFiles/after.yml');
		$actual2 = buildAST($beforeData2, $afterData2);
		$this->assertEquals($equals, $actual2);		
	}

	public function testGetPlain()
	{
		$equals = "Property 'common.setting2' was removed\nProperty 'common.setting6' was removed\nProperty 'common.setting4' was added with value: 'blah blah'\nProperty 'common.setting5' was added with value: 'complex value'\nProperty 'group1.baz' was changed. From 'bas' to 'bars'\nProperty 'group2' was removed\nProperty 'group3' was added with value: 'complex value'\n";

		$beforeData = \GenDiff\Parse\parser('tests/testFiles/before1.json');
		$afterData = \GenDiff\Parse\parser('tests/testFiles/after1.json');
		$ast = \GenDiff\Builder\buildAST($beforeData, $afterData);
		$actual = getPlain($ast);
		$this->assertEquals($equals, $actual);

		$equals2 = "Property 'timeout' was changed. From '50' to '20'\nProperty 'proxy' was removed\nProperty 'verbose' was added with value: 'true'\n";
		$beforeData2 = \GenDiff\Parse\parser('tests/testFiles/before.yml');
		$afterData2 = \GenDiff\Parse\parser('tests/testFiles/after.yml');
		$ast2 = \GenDiff\Builder\buildAST($beforeData2, $afterData2);
		$actual2 = getPlain($ast2);
		$this->assertEquals($equals2, $actual2);

	}

	public function testRender()
	{
		$equals = "    host: hexlet.io\n  + timeout: 20\n  - timeout: 50\n  - proxy: 123.234.53.22\n  + verbose: true\n";
		$beforeData = \GenDiff\Parse\parser('tests/testFiles/before.json');
		$afterData = \GenDiff\Parse\parser('tests/testFiles/after.json');
		$ast = \GenDiff\Builder\buildAST($beforeData, $afterData);
		$actual = render($ast, '');
		$this->assertEquals($equals, $actual);

		$beforeData2 = \GenDiff\Parse\parser('tests/testFiles/before.yml');
		$afterData2 = \GenDiff\Parse\parser('tests/testFiles/after.yml');
		$ast2 = \GenDiff\Builder\buildAST($beforeData2, $afterData2);
		$actual2 = render($ast2, '');
		$this->assertEquals($equals, $actual2);
	}
}
