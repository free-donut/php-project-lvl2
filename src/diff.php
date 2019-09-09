<?php
namespace GenDiff\Diff;

use \Funct\Collection;
use function GenDiff\Parser\parse;
use function GenDiff\Builder\buildAST;
use function GenDiff\Formatters\Render\getRender;
use function GenDiff\Formatters\Plain\getPlain;

function getData($filePath)
{
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
        $extention = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = parse($fileContent, $extention);
        return $data;
    } else {
        $e = new \Exception("'{$filePath}' is not exist\n");
        throw $e;
    }
}

function getDiff($pathToFile1, $pathToFile2, $format)
{
    $beforeData = getData($pathToFile1);
    $afterData = getData($pathToFile2);
    $ast = buildAST($beforeData, $afterData);

    switch ($format) {
        case 'json':
            $diff = getRender($ast);
            break;
        case 'plain':
            $diff = getPlain($ast);
            break;
        case 'pretty':
            $diff = json_encode($ast);
            break;
        default:
            $e = new \Exception("Format '{$format}' is not supported\n");
            throw $e;
            break;
    }
    return $diff;
}
