<?php

// testa o cabeçalho da requisição e o método HTTP que você escolheu no Thunder Client
// se der o erro 405, é porque você não escolheu o método correto no Thunder Client (GET, POST, PUT ou DELETE)

// Avisa ao Thunder Client que a resposta será sempre em formato JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// 1. Descobre qual botão você escolheu no Thunder Client (GET, POST, PUT ou DELETE)
$metodo = $_SERVER['REQUEST_METHOD'];

// 2. Captura o ID se você digitar ?id=10 na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// 3. Captura o JSON que você digitar na aba "Body" do Thunder Client
$dadosRecebidos = json_decode(file_get_contents("php://input"), true);

// Lista fictícia só para você ver os dados aparecendo no teste
$produtosFicticios = [
    ["id" => 1, "nome" => "Teclado Mecânico", "preco" => 250.00],
    ["id" => 2, "nome" => "Mouse Gamer", "preco" => 120.50],
    ["id" => 3, "nome" => "Monitor 24 Pol", "preco" => 890.00]
]; 

// inves de entregar para o banco de dados , ele vai converter para JSON e entregar para o Thunder Client, só para você ver que está funcionando.

// 4. Roteador REST
switch ($metodo) {

    case 'GET':
        http_response_code(200); // Status 200 OK
        if ($id) {
            echo json_encode([
                "metodo_detectado" => "GET (Busca Individual)",
                "mensagem" => "Você pediu apenas o produto de ID #$id"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "metodo_detectado" => "GET (Listar Todos)",
                "dados" => $produtosFicticios
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'POST':
        // Verifica se você escreveu o JSON lá na aba Body
        if (empty($dadosRecebidos['nome']) || empty($dadosRecebidos['preco'])) {
            http_response_code(400); // Status 400 Bad Request
            echo json_encode([
                "erro" => "Faltou enviar o JSON! Vá na aba Body -> JSON do Thunder Client e envie nome e preco."
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(201); // Status 201 Created
        echo json_encode([
            "metodo_detectado" => "POST (Criar Produto)",
            "mensagem" => "O PHP recebeu seu JSON com sucesso!",
            "produto_recebido" => $dadosRecebidos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "erro" => "Para usar o PUT, coloque ?id=1 no final da URL lá no Thunder Client!"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            "metodo_detectado" => "PUT (Atualizar Produto)",
            "mensagem" => "Produto #$id seria atualizado no banco com estes novos dados:",
            "novos_dados" => $dadosRecebidos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "erro" => "Para usar o DELETE, coloque ?id=1 no final da URL lá no Thunder Client!"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            "metodo_detectado" => "DELETE (Excluir Produto)",
            "mensagem" => "O produto #$id foi marcado para exclusão com sucesso!"
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>