<?php
require 'database.php';
header('Content-Type: application/json');

// Cria a conexão PDO
$db = getConnection();

// Lê o JSON enviado pela ESP
$data = json_decode(file_get_contents('php://input'), true);

try {
    if (isset($data['sensorTemperatura'])) {
        $stmt = $db->prepare("UPDATE sensores SET valor = ?, data_hora = NOW() WHERE id = 1");
        $stmt->execute([$data['sensorTemperatura']]);
    }

    if (isset($data['sensorUmidade'])) {
        $stmt = $db->prepare("UPDATE sensores SET valor = ?, data_hora = NOW() WHERE id = 3");
        $stmt->execute([$data['sensorUmidade']]);
    }

    echo json_encode(["status" => "sucesso", "mensagem" => "Leituras atualizadas com sucesso."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao atualizar: " . $e->getMessage()]);
}
?>
