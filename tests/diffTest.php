<?php

namespace GenDiff\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\Diff\getDiff;
use function GenDiff\Diff\getArray;
class diffTest extends TestCase
{
    public function testGetDiff()
    {
    	$equals = "{\n    host: hexlet.io\n  + timeout: 20\n  - timeout: 50\n  - proxy: 123.234.53.22\n  + verbose: 1\n}\n";

        $args = ["<firstFile>" => "before.json", "<secondFile>" => "after.json"];

        $actual = getDiff($args);

        $this->assertEquals($equals, $actual);
    }
}
