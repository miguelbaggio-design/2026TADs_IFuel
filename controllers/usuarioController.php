<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../dao/usuarioDAO.php';

/*
 * Recebe os formulários de cadastro, login e logout.
 * Cada <form method="POST"> deve enviar para controllers/usuarioController.php
 * com um campo escondido: <input type="hidden" name="acao" value="cadastrar|login|logout">
 *
 * Depois de cada ação, deixa em $_SESSION['mensagem'] um aviso
 * (['tipo' => 'sucesso'|'erro', 'texto' => '...']) para a próxima tela mostrar.
 */

/**
 * Retorna a lista de erros; vazia se cadastrou.
 */
function cadastrarUsuario(UsuarioDAO $dao, array $dados): array
{
    $usuario = new Usuario($dados['nome'] ?? '', $dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    $erros = Usuario::validarSenha($senha, $dados['confirmarSenha'] ?? '');
    if ($erros !== []) {
        return $erros;
    }

    $usuario->definirSenha($senha);
    $erros = $usuario->validar();
    if ($erros !== []) {
        return $erros;
    }

    if ($dao->emailExiste($usuario->getEmail())) {
        return ['Este e-mail já está cadastrado.'];
    }

    $dao->inserir($usuario);
    session_regenerate_id(true); // novo id de sessão ao entrar, por segurança
    $_SESSION['usuario_id'] = $usuario->getId();
    $_SESSION['usuario_nome'] = $usuario->getNome();
    return [];
}

/**
 * Trata o formulário enviado e devolve a página para onde redirecionar.
 */
function tratarRequisicaoUsuario(PDO $pdo, array $post): string
{
    $dao = new UsuarioDAO($pdo);

    switch ($post['acao'] ?? '') {
        case 'cadastrar':
            $erros = cadastrarUsuario($dao, $post);
            if ($erros !== []) {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => implode(' ', $erros)];
                return '../views/formCadastro.php';
            }
            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Conta criada com sucesso!'];
            return '../index.php';

        case 'login':
            $usuario = $dao->autenticar($post['email'] ?? '', $post['senha'] ?? '');
            if ($usuario === null) {
                $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'E-mail ou senha inválidos.'];
                return '../views/formLogin.php';
            }
            session_regenerate_id(true); // novo id de sessão ao entrar, por segurança
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_nome'] = $usuario->getNome();
            return '../index.php';

        case 'logout':
            unset($_SESSION['usuario_id'], $_SESSION['usuario_nome']);
            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Você saiu da sua conta.'];
            return '../views/formLogin.php';
    }

    return '../views/formLogin.php';
}

// Só age quando um formulário é enviado para este arquivo.
// A conexão vem de conexao(), que deve ser criada em config/config.php.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['acao'])) {
    require_once __DIR__ . '/../config/config.php';
    header('Location: ' . tratarRequisicaoUsuario(conexao(), $_POST));
    exit;
}
