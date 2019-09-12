<?php
namespace Differ;

use \Funct\Collection;
use function Differ\Parser\parse;
use function Differ\Builder\buildAST;
use function Differ\Formatters\Pretty\getPretty;
use function Differ\Formatters\Plain\getPlain;

function getData($filePath)
{
    if (!file_exists($filePath)) {
        $e = new \Exception("'{$filePath}' is not exist\n");
        throw $e;
    }
    $fileContent = file_get_contents($filePath);
    $extention = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = parse($fileContent, $extention);
    return $data;
}

function render($ast, $format)
{
    switch ($format) {
        case 'pretty':
            $rendering = getPretty($ast);
            break;
        case 'plain':
            $rendering = getPlain($ast);
            break;
        case 'json':
            $rendering = json_encode($ast);
            break;
        default:
            $e = new \Exception("Format '{$format}' is not supported\n");
            throw $e;
            break;
    }
    return $rendering;
}

function getDiff($pathToFile1, $pathToFile2, $format)
{
    $beforeData = getData($pathToFile1);
    $afterData = getData($pathToFile2);
    $ast = buildAST($beforeData, $afterData);
    $diff = render($ast, $format);
    return $diff;
}
