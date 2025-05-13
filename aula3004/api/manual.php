<?php
// Cabeçalhos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
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
$resource = $uri[1] ?? null;
$id = $uri[2] ?? null;

// Só vamos manipular a rota "/controle"
if ($resource !== 'controle') {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado"]);
    exit;
}

// Rotas
switch ($method) {
    case 'GET':
        if ($resource === 'controle') {
            getControleManual();
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Recurso não encontrado"]);
        }
        break;

    case 'PUT':
        if ($resource === 'controle') {
            updateControleManual();
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Recurso não encontrado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método não permitido"]);
}

// === FUNÇÕES ===
function getControleManual() {
    global $db;
    try {
        $stmt = $db->query("SELECT controle, atividade FROM controle");
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($controle);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]);
    }
}

function updateControleManual() {
    global $db;
    $data = getJsonInput();
    try {
        $stmt = $db->prepare("UPDATE controle SET atividade = ? WHERE controle = ?");
        $stmt->execute([$data['atividade'], $data['controle']]);
        echo json_encode(["message" => "Controle manual atualizado com sucesso"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]);
    }
}

