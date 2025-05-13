<?php
// COLOCA NO HTDOCS

// Cabeçalhos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclui config de banco
require_once __DIR__ . '/database.php';
$db = getConnection();

// Função para ler o corpo JSON
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

// Roteador
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $uri[2] ?? null; // Ex: "leituras"
$id = $uri[3] ?? null;

// Só vamos manipular a rota "/leituras"
if ($resource !== 'leituras') {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado"]);
    exit;
}

// Rotas
switch ($method) {
    case 'GET':
        if ($id) {
            getSensor($id);
        } else {
            getSensors();
        }
        break;

    case 'POST':
        createSensor();
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID é obrigatório para PUT"]);
            exit;
        }
        updateSensor($id);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID é obrigatório para DELETE"]);
            exit;
        }
        deleteSensor($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido"]);
}

// === FUNÇÕES ===

function getSensors() {
    global $db;
    try {
        $stmt = $db->query("SELECT s.*, l.* FROM sensores s LEFT JOIN leituras l ON s.id = l.id_sensor;");
        $sensors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($sensors);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao buscar sensores: " . $e->getMessage()]);
    }
}

function getSensor($id) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT s.*, l.* FROM sensores s LEFT JOIN leituras l ON s.id = l.id_sensor WHERE s.id = ?;");
        $stmt->execute([$id]);
        $sensor = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sensor) {
            echo json_encode($sensor);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Sensor não encontrado"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao buscar sensor: " . $e->getMessage()]);
    }
}

function createSensor() {
    global $db;
    $data = getJsonInput();
    if (!isset($data['luminosidade']) || !isset($data['temperatura']) || !isset($data['umidade']) || !isset($data['agua'])) {
        http_response_code(400);
        echo json_encode(["error" => "Campos obrigatórios: luminosidade, temperatura, umidade, nivel de água"]);
        return;
    }
    try {
        $stmt = $db->prepare("INSERT INTO leituras (luminosidade, temperatura, umidade, agua, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$data['luminosidade'], $data['temperatura'], $data['umidade'], $data['agua']]);
        echo json_encode(["message" => "Sensor criado com sucesso", "id" => $db->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao criar sensor: " . $e->getMessage()]);
    }
}

function updateSensor($id) {
    global $db;
    $data = getJsonInput();
    if (!isset($data['luminosidade']) || !isset($data['temperatura']) || !isset($data['umidade']) || !isset($data['agua'])) {
        http_response_code(400);
        echo json_encode(["error" => "Campos obrigatórios: luminosidade, temperatura, umidade, nível de água"]);
        return;
    }
    try {
        $stmt = $db->prepare("UPDATE leituras SET luminosidade = ?, temperatura = ?, umidade = ?, agua = ?, timestamp = NOW() WHERE id = ?");
        $stmt->execute([$data['luminosidade'], $data['temperatura'], $data['umidade'], $data['agua'], $id]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar sensor: " . $e->getMessage()]);
    }
}

function deleteSensor($id) {
    global $db;
    try {
        $stmt = $db->prepare("DELETE FROM leituras WHERE sensor_id = ?");
        $stmt->execute([$id]);

        $stmt = $db->prepare("DELETE FROM sensores WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["message" => "Sensor deletado com sucesso"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao deletar sensor: " . $e->getMessage()]);
    }
}

