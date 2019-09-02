<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Diff\getDiff;
use function GenDiff\Parse\parser;
use function GenDiff\Builder\buildAST;
use function GenDiff\Render\getRender;
use function GenDiff\Plain\getPlain;


class DiffTest extends TestCase
{
    private $pathToTestFile;
    private $pathToExpectedFile;

    public function setUp() : void
    {
        $this->pathToExpectedFile = __DIR__ . '/fixtures/';
        $this->pathToTestFile = __DIR__ . '/testFiles/';
    }

    /**
    * @dataProvider additionProviderDiff
    */

    public function testGetDiff($expectedFile, $beforeFile, $afterFile, $format)
    {
        $expected = file_get_contents($this->pathToExpectedFile . $expectedFile);
        $pathToBeforeFile = $this->pathToTestFile . $beforeFile;
        $pathToAfterFile = $this->pathToTestFile . $afterFile;
        $actual = getDiff($pathToBeforeFile, $pathToAfterFile, $format);
        $this->assertEquals($expected, $actual);
    }

    public function additionProviderDiff()
    {
        return [
            ['json_flat', 'before.json', 'after.json', 'json'],
            ['json_nested', 'before1.json', 'after1.json', 'json'],
            ['plain_flat', 'before.yml', 'after.yml', 'plain'],
            ['plain_nested', 'before1.json', 'after1.json', 'plain']
        ];
    }

    /**
    * @dataProvider additionProviderParser
    */

    public function testParser($expected, $file)
    {
        $pathToFile = $this->pathToTestFile . $file;
        $actual = parser($pathToFile);
        $this->assertEquals($expected, $actual);
    }

    public function additionProviderParser()
    {
        $out = [];

        $out[] =  [
            ["host" => "hexlet.io", "timeout" => 50, "proxy" => "123.234.53.22"],
            'before.json'
        ];

        $out[] =  [
            ["host" => "hexlet.io", "timeout" => 50, "proxy" => "123.234.53.22"],
            'before.yml'
        ];

        return $out;
    }

    /**
    * @dataProvider additionProviderBuilder
    */

    public function testBuildAST($expected, $beforeFile, $afterFile)
    {
        
        $expected = require($this->pathToExpectedFile . $expected);
        $beforeData = parser($this->pathToTestFile . $beforeFile);
        $afterData = parser($this->pathToTestFile . $afterFile);
        $actual = buildAST($beforeData, $afterData);
        $this->assertEquals($expected, $actual);
    }

    public function additionProviderBuilder()
    {
        return [
            ['expected_ast.php', 'before.json', 'after.json'],
            ['expected_ast.php', 'before.yml', 'after.yml']
        ];
    }

    /**
    * @dataProvider additionProviderRender
    */

    public function testGetRender($expectedFile, $beforeFile, $afterFile)
    {
        $expected = file_get_contents($this->pathToExpectedFile . $expectedFile);
        $beforeData = \GenDiff\Parse\parser($this->pathToTestFile . $beforeFile);
        $afterData = \GenDiff\Parse\parser($this->pathToTestFile . $afterFile);
        $ast = \GenDiff\Builder\buildAST($beforeData, $afterData);
        $actual = getRender($ast, '');
        $this->assertEquals($expected, $actual);
    }

    public function additionProviderRender()
    {
        return [
            ['json_flat', 'before.json', 'after.json'],
            ['json_flat', 'before.yml', 'after.yml'],
            ['json_nested', 'before1.json', 'after1.json']
        ];
    }

    /**
    * @dataProvider additionProviderPlain
    */

    public function testGetPlain($expectedFile, $beforeFile, $afterFile)
    {
        $expected = file_get_contents($this->pathToExpectedFile . $expectedFile);
        $beforeData = \GenDiff\Parse\parser($this->pathToTestFile . $beforeFile);
        $afterData = \GenDiff\Parse\parser($this->pathToTestFile . $afterFile);
        $ast = \GenDiff\Builder\buildAST($beforeData, $afterData);
        $actual = getPlain($ast);
        $this->assertEquals($expected, $actual);
    }

    public function additionProviderPlain()
    {
        return [
            ['plain_flat', 'before.json', 'after.json'],
            ['plain_nested', 'before1.json', 'after1.json']
        ];
    }
}
