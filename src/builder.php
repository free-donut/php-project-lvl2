<?php
namespace GenDiff\Builder;

use \Funct\Collection;

function buildNode($type, $key, $beforeValue, $afterValue, $child = null)
{
	$node = [
	'type' => $type,
	'key' => $key,
	'beforeValue' => $beforeValue,
	'afterValue' => $afterValue,
	'child' => $child
	];
	return $node;
}

function buildAST($beforeData, $afterData)
{
	$unionKeys = Collection\union(array_keys($beforeData), array_keys($afterData));
	
	$ast = array_map(function ($key) use ($beforeData, $afterData) {
		//если существуют оба значения
		if (isset($beforeData[$key]) && isset($afterData[$key])) {
			if ($beforeData[$key] == $afterData[$key]) {
			//вернуть node с типом "без изменений"
				$node = buildNode('unchanged', $key, $beforeData[$key], $afterData[$key], '');
				return $node;
			//если оба значения - массивы
			} elseif (is_array($beforeData[$key]) && is_array($afterData[$key])) {
			//вернуть вложенный ast
				$child = buildAST($beforeData[$key], $afterData[$key]);
				$node = buildNode('array', $key, null, null, $child);
			} else {
			//венуть node с типом "изменен"
				$node = buildNode('changed', $key, $beforeData[$key], $afterData[$key], '');
			}
		// если существует только первое значение
		} elseif (isset($beforeData[$key]) && !isset($afterData[$key])) {
		//вернуть node с типом "удален"
			$node = buildNode('deleted', $key, $beforeData[$key], null, '');
		//если существует только второе значение
		} else {
		//вернуть node со значением "добавен"
			$node = buildNode('added', $key, null, $afterData[$key], '');
		}
		return $node;
	}, $unionKeys);

	return array_values($ast);
}