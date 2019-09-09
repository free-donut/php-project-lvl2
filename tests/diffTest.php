<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Diff\getDiff;

class DiffTest extends TestCase
{
    private $pathToTestFile;
    private $pathToExpectedFile;

    public function setUp() : void
    {
        $this->pathToExpectedFile = __DIR__ . '/fixtures/expected/';
        $this->pathToTestedFile = __DIR__ . '/fixtures/testable/';
    }

    /**
    * @dataProvider additionProviderDiff
    */

    public function testGetDiff($expectedFile, $beforeFile, $afterFile, $format)
    {
        $expected = file_get_contents($this->pathToExpectedFile . $expectedFile);
        $pathToBeforeFile = $this->pathToTestedFile . $beforeFile;
        $pathToAfterFile = $this->pathToTestedFile . $afterFile;
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
}
