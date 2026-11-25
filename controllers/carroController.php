<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dao/carroDAO.php';

/*
 * Recebe os formulários de veículo.
 * Cada <form method="POST"> deve enviar para controllers/carroController.php com:
 *   <input type="hidden" name="acao" value="salvar|excluir">
 *   <input type="hidden" name="id" value="..."> (só para editar ou excluir)
 *
 * Só mexe nos carros do usuário logado ($_SESSION['usuario_id']).
 * Depois de cada ação, deixa um aviso em $_SESSION['mensagem'].
 */

/**
 * @return Carro[]
 */
function listarCarrosDoUsuario(CarroDAO $dao, int $usuarioId): array
{
    return $dao->listar($usuarioId);
}

/**
 * Retorna o carro só se ele pertencer ao usuário.
 */
function buscarCarroDoUsuario(CarroDAO $dao, int $id, int $usuarioId): ?Carro
{
    $carro = $dao->buscarPorId($id);
    if ($carro === null || $carro->getUsuarioId() !== $usuarioId) {
        return null;
    }
    return $carro;
}

/**
 * Cadastra (sem id) ou edita (com id). Retorna a lista de erros; vazia se salvou.
 */
function salvarCarro(CarroDAO $dao, array $dados, int $usuarioId): array
{
    $carro = Carro::fromArray($dados);
    $carro->setUsuarioId($usuarioId); // o dono é sempre quem está logado

    $erros = $carro->validar();
    if ($erros !== []) {
        return $erros;
    }

    if ($carro->getId() === null) {
        $dao->inserir($carro);
        return [];
    }

    if (buscarCarroDoUsuario($dao, $carro->getId(), $usuarioId) === null) {
        return ['Veículo não encontrado.'];
    }
    $dao->atualizar($carro);
    return [];
}

/**
 * Trata o formulário enviado e devolve a página para onde redirecionar.
 */
function tratarRequisicaoCarro(PDO $pdo, array $post): string
{
    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Faça login para continuar.'];
        return '../views/formLogin.php';
    }

    $usuarioId = (int) $_SESSION['usuario_id'];
    $dao = new CarroDAO($pdo);

    switch ($post['acao'] ?? '') {
        case 'salvar':
            $erros = salvarCarro($dao, $post, $usuarioId);
            if ($erros !== []) {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => implode(' ', $erros)];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Veículo salvo.'];
            }
            break;

        case 'excluir':
            $id = (int) ($post['id'] ?? 0);
            // Excluir o carro também apaga os abastecimentos dele (ON DELETE CASCADE no banco).
            if (buscarCarroDoUsuario($dao, $id, $usuarioId) !== null && $dao->excluir($id)) {
                $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Veículo excluído.'];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Veículo não encontrado.'];
            }
            break;
    }

    return '../views/formCarro.php';
}

// Só age quando um formulário é enviado para este arquivo.
// A conexão vem de conexao(), que deve ser criada em config/config.php.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['acao'])) {
    require_once __DIR__ . '/../config/config.php';
    header('Location: ' . tratarRequisicaoCarro(conexao(), $_POST));
    exit;
}
