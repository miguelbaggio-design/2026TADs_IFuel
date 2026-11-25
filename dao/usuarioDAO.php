<?php

require_once __DIR__ . '/../models/usuarioModel.php';

/**
 * Acesso a dados da tabela `usuario`.
 * Recebe a conexão PDO pronta, para não depender de como o config.php a cria.
 */
class UsuarioDAO
{
    private PDO $conexao;

    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Insere o usuário e preenche o id gerado no próprio objeto.
     * O e-mail é único no banco: um e-mail repetido gera PDOException.
     * Use emailExiste() antes para mostrar uma mensagem amigável.
     */
    public function inserir(Usuario $usuario): Usuario
    {
        $sql = 'INSERT INTO usuario (nome, email, senha_hash)
                VALUES (:nome, :email, :senha_hash)';

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha_hash', $usuario->getSenhaHash());
        $stmt->execute();

        $usuario->setId((int) $this->conexao->lastInsertId());
        return $usuario;
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $stmt = $this->conexao->prepare('SELECT * FROM usuario WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $linha ? Usuario::fromArray($linha) : null;
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        $stmt = $this->conexao->prepare('SELECT * FROM usuario WHERE email = :email');
        $stmt->bindValue(':email', Usuario::normalizarEmail($email));
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $linha ? Usuario::fromArray($linha) : null;
    }

    /**
     * Informe $ignorarId ao editar, para não acusar o próprio usuário.
     */
    public function emailExiste(string $email, ?int $ignorarId = null): bool
    {
        $usuario = $this->buscarPorEmail($email);
        return $usuario !== null && $usuario->getId() !== $ignorarId;
    }

    /**
     * Retorna o usuário se e-mail e senha conferem; senão, null.
     */
    public function autenticar(string $email, string $senha): ?Usuario
    {
        $usuario = $this->buscarPorEmail($email);
        if ($usuario === null || !$usuario->verificarSenha($senha)) {
            return null;
        }
        return $usuario;
    }

    /**
     * @return Usuario[]
     */
    public function listar(): array
    {
        $stmt = $this->conexao->query('SELECT * FROM usuario ORDER BY nome');

        $usuarios = [];
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = Usuario::fromArray($linha);
        }
        return $usuarios;
    }

    /**
     * Atualiza nome, e-mail e hash da senha.
     * Retorna false se nenhuma linha mudou. No MySQL isso também acontece
     * quando o id existe mas os valores enviados são iguais aos gravados.
     */
    public function atualizar(Usuario $usuario): bool
    {
        if ($usuario->getId() === null) {
            throw new InvalidArgumentException('Não é possível atualizar um usuário sem id.');
        }

        $sql = 'UPDATE usuario
                   SET nome = :nome, email = :email, senha_hash = :senha_hash
                 WHERE id = :id';

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha_hash', $usuario->getSenhaHash());
        $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->conexao->prepare('DELETE FROM usuario WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
