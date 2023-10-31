<?php

namespace Telnet;

class ConvTree {
    private array $originalData;
    private array $nodeIdToPath = [];

    public function __construct(string $jsonString) {
        $this->originalData = json_decode($jsonString, true);
    }

    public function getTree(): array {
        $structure = [];
        foreach ($this->originalData as $oneElement) {
            $pathElements = explode(',', $oneElement['namePath']);
            $temp = &$structure;
            for ($i = 0, $size = count($pathElements); $i < $size; $i++) {
                if ($i == ($size - 1)) {
                    $temp[] = $oneElement;
                } else {
                    if (!isset($temp[$pathElements[$i]])) {
                        $temp[$pathElements[$i]] = [];
                    }
                    $temp = &$temp[$pathElements[$i]];
                }
            }
        }
        $tree = [];
        foreach ($structure as $category => $categoryData) {
            $tree[] = $this->generateStructuredData($category, $categoryData, [], []);
        }
        return $tree;
    }

    private function generateStructuredData(string $nodeName, array $nodeData, array $path, array $idPath): array {
        $path[] = $nodeName;
        $nodeId = $this->getNodeId($path);
        $idPath[] = $nodeId;
        $children = [];
        foreach ($nodeData as $key => $value) {
            if (is_int($key)) {
                $oneNodeId = $this->getNodeId(array_merge($path, [$value['name']]));
                $children[] = [
                    'id' => $oneNodeId,
                    'id_path' => ',' . implode(',', $idPath) . ',' . $oneNodeId . ',',
                    'is_leaf' => 1,
                    'level' => count($path) + 1,
                    'name' => $value['name'],
                    'name_path' => $value['namePath'],
                    'parent_id' => $nodeId,
                ];
            } else {
                $children[] = $this->generateStructuredData($key, $value, $path, $idPath);
            }
        }
        $nodeGeneratedData = [
            'id' => $nodeId,
            'id_path' => ',' . implode(',', $idPath) . ',',
            'is_leaf' => empty($children) ? 1 : 2,
            'level' => count($path),
            'name' => $nodeName,
            'name_path' => implode(',', $path),
            'parent_id' => count($idPath) > 1 ? $idPath[count($idPath) - 2] : 0,
        ];
        if (!empty($children)) {
            $nodeGeneratedData['children'] = $children;
        }
        return $nodeGeneratedData;
    }

    private function getNodeId(array $path): string {
        $pathStr = implode(',', $path);
        $id = substr(md5($pathStr), 22);
        $i = 0;
        while (isset($this->nodeIdToPath[$id])) {
            $id = substr(md5($pathStr . $i++), 22);
        }
        $this->nodeIdToPath[$id] = $pathStr;
        return $id;
    }
}