<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../services/consumo.php';
require_once __DIR__ . '/../dao/carroDAO.php';
require_once __DIR__ . '/../dao/abastecimentoDAO.php';

/*
 * Recebe os formulários de abastecimento.
 * Cada <form method="POST"> deve enviar para controllers/abastecimentoController.php com:
 *   <input type="hidden" name="acao" value="salvar|excluir">
 *   <input type="hidden" name="id" value="..."> (só para editar ou excluir)
 *
 * Só mexe em abastecimentos de carros do usuário logado ($_SESSION['usuario_id']).
 * Depois de cada ação, deixa um aviso em $_SESSION['mensagem'].
 */

function mapearAbastecimento(array $entrada): array
{
    $litros = campoNumerico($entrada, 'litros');
    $valor = campoNumerico($entrada, 'valor_gasto');

    $precoLitro = null;
    if ($litros !== null && $litros > 0 && $valor !== null) {
        $precoLitro = $valor / $litros;
    }

    return [
        'odometro' => campoNumerico($entrada, 'km'),
        'qtd_abast' => $litros,
        'preco_litro' => $precoLitro,
        'data' => $entrada['data'] ?? null,
    ];
}

function metricasDoFormulario(array $abastecimentos, $tanque): array
{
    $normalizados = [];
    foreach ($abastecimentos as $item) {
        $normalizados[] = mapearAbastecimento($item);
    }

    return calcularMetricas($normalizados, $tanque);
}

function campoNumerico(array $entrada, string $campo)
{
    if (!isset($entrada[$campo]) || !is_numeric($entrada[$campo])) {
        return null;
    }

    return $entrada[$campo] + 0;
}

/**
 * Histórico de todos os carros do usuário, do mais recente para o mais antigo.
 *
 * @return Abastecimento[]
 */
function listarAbastecimentosDoUsuario(AbastecimentoDAO $dao, int $usuarioId): array
{
    return $dao->listarPorUsuario($usuarioId);
}

/**
 * Métricas de consumo (km/l, custo/km, autonomia) de um carro,
 * calculadas por services/consumo.php a partir do histórico salvo.
 */
function metricasDoCarro(AbastecimentoDAO $dao, Carro $carro): array
{
    $itens = [];
    foreach ($dao->listarPorCarro($carro->getId()) as $abastecimento) {
        $itens[] = $abastecimento->paraMetricas();
    }
    return calcularMetricas($itens, $carro->getTanque());
}

/**
 * Retorna o carro só se ele pertencer ao usuário.
 */
function carroDoUsuario(CarroDAO $carroDao, int $carroId, int $usuarioId): ?Carro
{
    $carro = $carroDao->buscarPorId($carroId);
    if ($carro === null || $carro->getUsuarioId() !== $usuarioId) {
        return null;
    }
    return $carro;
}

/**
 * Retorna o abastecimento só se o carro dele pertencer ao usuário.
 */
function buscarAbastecimentoDoUsuario(AbastecimentoDAO $dao, CarroDAO $carroDao, int $id, int $usuarioId): ?Abastecimento
{
    $abastecimento = $dao->buscarPorId($id);
    if ($abastecimento === null || carroDoUsuario($carroDao, $abastecimento->getCarroId(), $usuarioId) === null) {
        return null;
    }
    return $abastecimento;
}

/**
 * Registra (sem id) ou edita (com id). Retorna a lista de erros; vazia se salvou.
 * Campos do formulário: carro_id, km, valor_gasto, litros (opcional).
 */
function salvarAbastecimento(AbastecimentoDAO $dao, CarroDAO $carroDao, array $dados, int $usuarioId): array
{
    $abastecimento = Abastecimento::fromArray($dados);

    $erros = $abastecimento->validar();
    if ($erros !== []) {
        return $erros;
    }
    if (carroDoUsuario($carroDao, $abastecimento->getCarroId(), $usuarioId) === null) {
        return ['Selecione um dos seus veículos.'];
    }

    if ($abastecimento->getId() === null) {
        $dao->inserir($abastecimento); // grava com a data atual
        return [];
    }

    $existente = buscarAbastecimentoDoUsuario($dao, $carroDao, $abastecimento->getId(), $usuarioId);
    if ($existente === null) {
        return ['Abastecimento não encontrado.'];
    }
    $abastecimento->setData($existente->getData()); // o formulário não tem data: mantém a original
    $dao->atualizar($abastecimento);
    return [];
}

/**
 * Trata o formulário enviado e devolve a página para onde redirecionar.
 */
function tratarRequisicaoAbastecimento(PDO $pdo, array $post): string
{
    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Faça login para continuar.'];
        return '../views/formLogin.php';
    }

    $usuarioId = (int) $_SESSION['usuario_id'];
    $dao = new AbastecimentoDAO($pdo);
    $carroDao = new CarroDAO($pdo);

    switch ($post['acao'] ?? '') {
        case 'salvar':
            $erros = salvarAbastecimento($dao, $carroDao, $post, $usuarioId);
            if ($erros !== []) {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => implode(' ', $erros)];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Abastecimento salvo.'];
            }
            break;

        case 'excluir':
            $id = (int) ($post['id'] ?? 0);
            if (buscarAbastecimentoDoUsuario($dao, $carroDao, $id, $usuarioId) !== null && $dao->excluir($id)) {
                $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Abastecimento excluído.'];
            } else {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Abastecimento não encontrado.'];
            }
            break;
    }

    return '../views/formAbastecimento.php';
}

// Só age quando um formulário é enviado para este arquivo.
// A conexão vem de conexao(), que deve ser criada em config/config.php.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['acao'])) {
    require_once __DIR__ . '/../config/config.php';
    header('Location: ' . tratarRequisicaoAbastecimento(conexao(), $_POST));
    exit;
}
