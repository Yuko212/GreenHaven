<?php
// COLOCA NO HTDOCS

// CORS headers (in your PHP file handling the requests)
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem. Pode-se especificar um domínio.
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permite cabeçalhos específicos nas requisições.
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Permite métodos HTTP específicos.
    header("Access-Control-Allow-Credentials: true"); // Permite o envio de cookies e credenciais.
    
// Verifica se o método da requisição é OPTIONS (usado para pré-verificações CORS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem.
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Permite métodos HTTP.
        header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Permite cabeçalhos específicos.
        header('Access-Control-Max-Age: 86400'); // Indica que a resposta pode ser armazenada em cache por 1 dia.
        http_response_code(200); // Retorna status 200 (OK).
        exit; // Encerra a execução do script.
    }
// Inclui config de banco
require_once __DIR__ . '/database.php'; // Inclui o arquivo que contém a função de conexão com o banco de dados.
$db = getConnection(); // Obtém a conexão com o banco de dados.

// Função para ler o corpo JSON
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true); // Lê o corpo da requisição e decodifica o JSON.
}

// Roteador
$method = $_SERVER['REQUEST_METHOD']; // Obtém o método HTTP da requisição.
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));  // Divide a URI em partes.
$resource = $uri[3] ?? null;  // Obtém o recurso da URI (ex: "sensores").
$id = $uri[4] ?? null; // Obtém o ID do recurso da URI.

// Rotas
if($resource == 'sensores')
{
    switch ($method) {
        case 'GET': // Se o método for GET
            if ($id) {
                getSensor($id); // Chama a função para obter um sensor específico.
            } else {
                getSensors();  // Chama a função para obter todos os sensores.
            }
            break;
    
        case 'POST': // Se o método for POST
            createSensor(); // Chama a função para criar um novo sensor.
            break;
    
        case 'PUT': // Se o método for PUT
            if (!$id) {
                http_response_code(400); // Retorna status 400 (requisição inválida) se o ID não for fornecido.
                echo json_encode(["error" => "ID é obrigatório para PUT"]); // Retorna um erro em formato JSON.
                exit;
            } 
            updateSensor($id); // Chama a função para atualizar um sensor existente.
            break;
    
        case 'DELETE': // Se o método for DELETE
            if (!$id) {
                http_response_code(400); // Retorna status 400 (requisição inválida) se o ID não for fornecido.
                echo json_encode(["error" => "ID é obrigatório para DELETE"]); // Retorna um erro em formato JSON.
                exit; // Encerra a execução do script.
            }
            deleteSensor($id); // Chama a função para deletar um sensor.
            break;
    
        default: // Para qualquer outro método não permitido
            http_response_code(405); // Retorna status 405 (método não permitido).
            echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    } 
}
else if($resource == 'controle')
{
    // Rotas
    switch ($method) {
        case 'GET': // Se o método for GET
            if ($resource === 'controle') { 
                getControleManual(); // Chama a função para obter o controle manual.
            } else {
                http_response_code(404); // Retorna status 404 (não encontrado) se o recurso não for "controle".
                echo json_encode(["error" => "Recurso não encontrado"]); // Retorna um erro em formato JSON.
            }
            break;

        case 'PUT': // Se o método for PUT
            if ($resource === 'controle') {
                updateControleManual();  // Chama a função para atualizar o controle manual.
            } else {
                http_response_code(404); // Retorna status 404 (não encontrado) se o recurso não for "controle".
                echo json_encode(["error" => "Recurso não encontrado"]); // Retorna um erro em formato JSON.
            }
            break;

        default: // Para qualquer outro método não permitido
            http_response_code(405); // Retorna status 405 (método não permitido).
            echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    }
}

else if ($resource == 'iluminacao') {
    switch ($method) {
        case 'GET':
            getIluminacao();
            break;
        case 'PUT':
            updateIluminacao();
            break;
        default: // Para qualquer outro método não permitido
        http_response_code(405); // Retorna status 405 (método não permitido).
        echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    }
}
else if ($resource == 'irrigacao') {
    switch ($method) {
        case 'GET':
            getIrrigacao();
            break;
        case 'PUT':
            updateIrrigacao();
            break;
        default: // Para qualquer outro método não permitido
        http_response_code(405); // Retorna status 405 (método não permitido).
        echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    }
}
else if ($resource == 'aquecimento') {
    switch ($method) {
        case 'GET':
            getAquecimento();
            break;
        case 'PUT':
            updateAquecimento();
            break;
        default: // Para qualquer outro método não permitido
        http_response_code(405); // Retorna status 405 (método não permitido).
        echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    }
}
else if ($resource == 'ventilacao') {
    switch ($method) {
        case 'GET':
            getVentilacao();
            break;
        case 'PUT':
            updateVentilacao();
            break;
        default: // Para qualquer outro método não permitido
        http_response_code(405); // Retorna status 405 (método não permitido).
        echo json_encode(["error" => "Método não permitido"]); // Retorna um erro em formato JSON.
    }
}

else
{
    http_response_code(404); // Retorna status 404 (não encontrado) se o recurso não for "controle".
    echo json_encode(["error" => "Endpoint Inexistente"]); // Retorna um erro em formato JSON.
}


// === FUNÇÕES ===

function getSensors() { // Função para obter todos os sensores
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM sensores;"); // Executa uma consulta para obter todos os sensores.
        $sensors = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($sensors); // Retorna os sensores em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar sensores: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}
// Função para obter um sensor específico pelo ID
function getSensor($id) {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->prepare("SELECT * FROM sensores s WHERE s.id = ?;"); // Prepara a consulta para obter um sensor específico.
        $stmt->execute([$id]); // Executa a consulta passando o ID.
        $sensor = $stmt->fetch(PDO::FETCH_ASSOC); // Busca o resultado como um array associativo.
        if ($sensor) {
            echo json_encode($sensor); // Retorna o sensor em formato JSON se encontrado.
        } else {
            http_response_code(404); // Retorna status 404 (não encontrado) se o sensor não existir.
            echo json_encode(["error" => "Sensor não encontrado"]); // Retorna um erro em formato JSON.
        }
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar sensor: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

// Função para criar um novo sensor
function createSensor() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.
    // Verifica se os campos obrigatórios estão presentes
    if (!isset($data['tipo']) || !isset($data['unidade']) || !isset($data['valor'])) {
        http_response_code(400); // Retorna status 400 (requisição inválida) se algum campo estiver faltando.
        echo json_encode(["error" => "Campos obrigatórios: tipo, unidade, valor"]); // Retorna um erro em formato JSON.
        return; // Encerra a execução da função.
    }
    try {
        // Prepara a consulta para inserir um novo sensor
        $stmt = $db->prepare("INSERT INTO sensores (tipo, unidade, valor, data_hora) VALUES (?, ?, ?, now())");
        $stmt->execute([$data['tipo'], $data['unidade'], $data['valor']]); // Executa a consulta com os dados fornecidos.
        echo json_encode(["message" => "Sensor criado com sucesso", "id" => $db->lastInsertId()]); // Retorna uma mensagem de sucesso e o ID do novo sensor.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao criar sensor: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    } 
}
// Função para atualizar um sensor existente
function updateSensor($id) {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.
    // Verifica se os campos obrigatórios estão presentes
    if (!isset($data['tipo']) || !isset($data['unidade']) || !isset($data['valor'])) {
        http_response_code(400); // Retorna status 400 (requisição inválida) se algum campo estiver faltando.
        echo json_encode(["error" => "Campos obrigatórios: tipo, unidade, valor"]); // Retorna um erro em formato JSON.
        return; // Encerra a execução da função.
    }
    try { // Prepara a consulta para atualizar um sensor existente
        $stmt = $db->prepare("UPDATE sensores SET unidade = ?, valor = ?, data_hora = NOW() WHERE id = ?");
        $stmt->execute([$data['unidade'], $data['valor'], $id]); // Executa a consulta com os dados fornecidos.
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar sensor: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}
// Função para deletar um sensor existente
function deleteSensor($id) {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
         // Prepara a consulta para deletar um sensor
        $stmt = $db->prepare("DELETE FROM sensores WHERE id = ?");
        $stmt->execute([$id]); // Executa a consulta passando o ID do sensor a ser deletado.

        echo json_encode(["message" => "Sensor deletado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao deletar sensor: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}



// === FUNÇÕES MANUAL===
function getControleManual() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM controle"); // Executa uma consulta para obter os dados do controle.
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($controle); // Retorna os dados do controle em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

// Função para obter o controle manual
function updateControleManual() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.

    try {
        $stmt = $db->prepare("UPDATE controle SET atividade = ? WHERE id = 1");
        $stmt->execute([$data['atividade']]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}


// === FUNÇÕES ILUMINAÇÃO===
function getIluminacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM `atuadores` WHERE id = 1;"); // Executa uma consulta para obter os dados do controle.
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($controle); // Retorna os dados do controle em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

function updateIluminacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.

    try {
        $stmt = $db->prepare("UPDATE atuadores SET status = ? WHERE id = 1");
        $stmt->execute([$data['status']]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

// === FUNÇÕES IRRIGAÇÃO===
function getIrrigacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM `atuadores` WHERE id = 2;"); // Executa uma consulta para obter os dados do controle.
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($controle); // Retorna os dados do controle em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

function updateIrrigacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.

    try {
        $stmt = $db->prepare("UPDATE atuadores SET status = ? WHERE id = 2");
        $stmt->execute([$data['status']]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

// === FUNÇÕES AQUECIMENTO===
function getAquecimento() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM `atuadores` WHERE id = 3;"); // Executa uma consulta para obter os dados do controle.
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($controle); // Retorna os dados do controle em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

function updateAquecimento() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.

    try {
        $stmt = $db->prepare("UPDATE atuadores SET status = ? WHERE id = 3");
        $stmt->execute([$data['status']]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

// === FUNÇÕES VENTILAÇÃO===
function getVentilacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    try {
        $stmt = $db->query("SELECT * FROM `atuadores` WHERE id = 4;"); // Executa uma consulta para obter os dados do controle.
        $controle = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo.
        echo json_encode($controle); // Retorna os dados do controle em formato JSON.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao buscar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

function updateVentilacao() {
    global $db; // Acessa a variável global de conexão com o banco de dados.
    $data = getJsonInput(); // Obtém os dados da requisição em formato JSON.

    try {
        $stmt = $db->prepare("UPDATE atuadores SET status = ? WHERE id = 4");
        $stmt->execute([$data['status']]);
        echo json_encode(["message" => "Sensor atualizado com sucesso"]); // Retorna uma mensagem de sucesso.
    } catch (PDOException $e) {
        http_response_code(500); // Retorna status 500 (erro interno do servidor) em caso de falha.
        echo json_encode(["error" => "Erro ao atualizar controle manual: " . $e->getMessage()]); // Retorna um erro em formato JSON.
    }
}

