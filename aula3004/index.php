<?php
header("Content-Type: application/json");

// Diretório atual
$directory = __DIR__;

// Lista os arquivos e pastas
$files = array_diff(scandir($directory), ['.', '..']);

// Cria uma estrutura para o JSON
$result = [];
foreach ($files as $file) {
    $result[] = [
        'name' => $file,
        'type' => is_dir($directory . DIRECTORY_SEPARATOR . $file) ? 'directory' : 'file',
        'size' => is_file($directory . DIRECTORY_SEPARATOR . $file) ? filesize($directory . DIRECTORY_SEPARATOR . $file) : null,
        'last_modified' => date("Y-m-d H:i:s", filemtime($directory . DIRECTORY_SEPARATOR . $file)),
    ];
}

// Retorna o JSON
echo json_encode($result, JSON_PRETTY_PRINT);