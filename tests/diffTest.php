<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Diff\getDiff;
use function GenDiff\Diff\getArray;
use function GenDiff\Builder\buildAST;
use function GenDiff\Parse\parser;

class diffTest extends TestCase
{
    public function testGetDiff()
    {
    	$equals = "{\n    host: hexlet.io\n  + timeout: 20\n  - timeout: 50\n  - proxy: 123.234.53.22\n  + verbose: true\n}\n";

        $actual = getDiff('tests/testFiles/before.json', 'tests/testFiles/after.json');

        $this->assertEquals($equals, $actual);

        $equals2 = "{\n    common: {\n        setting1: Value 1\n      - setting2: 200\n        setting3: true\n      - setting6: {\n            key: value\n        }\n      + setting4: blah blah\n      + setting5: {\n            key5: value5\n        }\n    }\n    group1: {\n      + baz: bars\n      - baz: bas\n        foo: bar\n    }\n  - group2: {\n        abc: 12345\n    }\n  + group3: {\n        fee: 100500\n    }\n}\n";
        $actual2 = getDiff('tests/testFiles/before1.json', 'tests/testFiles/after1.json');
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
    }
}
