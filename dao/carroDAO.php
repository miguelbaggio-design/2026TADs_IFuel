<?php

require_once __DIR__ . '/../models/carroModel.php';

/**
 * Acesso a dados da tabela `carro`.
 * Recebe a conexão PDO pronta, para não depender de como o config.php a cria.
 */
class CarroDAO
{
    private PDO $conexao;

    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Insere o carro e preenche o id gerado no próprio objeto.
     */
    public function inserir(Carro $carro): Carro
    {
        $sql = 'INSERT INTO carro (usuario_id, modelo, ano, km, tanque)
                VALUES (:usuario_id, :modelo, :ano, :km, :tanque)';

        $stmt = $this->conexao->prepare($sql);
        $this->vincularCampos($stmt, $carro);
        $stmt->execute();

        $carro->setId((int) $this->conexao->lastInsertId());
        return $carro;
    }

    public function buscarPorId(int $id): ?Carro
    {
        $stmt = $this->conexao->prepare('SELECT * FROM carro WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $linha ? Carro::fromArray($linha) : null;
    }

    /**
     * Lista os carros, opcionalmente filtrando pelo proprietário.
     *
     * @return Carro[]
     */
    public function listar(?int $usuarioId = null): array
    {
        if ($usuarioId === null) {
            $stmt = $this->conexao->prepare('SELECT * FROM carro ORDER BY modelo');
        } else {
            $stmt = $this->conexao->prepare('SELECT * FROM carro WHERE usuario_id = :usuario_id ORDER BY modelo');
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        }
        $stmt->execute();

        $carros = [];
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $carros[] = Carro::fromArray($linha);
        }
        return $carros;
    }

    /**
     * Retorna false se nenhuma linha mudou. No MySQL isso também acontece
     * quando o id existe mas os valores enviados são iguais aos gravados.
     */
    public function atualizar(Carro $carro): bool
    {
        if ($carro->getId() === null) {
            throw new InvalidArgumentException('Não é possível atualizar um carro sem id.');
        }

        $sql = 'UPDATE carro
                   SET usuario_id = :usuario_id, modelo = :modelo, ano = :ano, km = :km, tanque = :tanque
                 WHERE id = :id';

        $stmt = $this->conexao->prepare($sql);
        $this->vincularCampos($stmt, $carro);
        $stmt->bindValue(':id', $carro->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->conexao->prepare('DELETE FROM carro WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function vincularCampos(PDOStatement $stmt, Carro $carro): void
    {
        $usuarioId = $carro->getUsuarioId();
        $stmt->bindValue(':usuario_id', $usuarioId, $usuarioId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':modelo', $carro->getModelo());
        $stmt->bindValue(':ano', $carro->getAno(), PDO::PARAM_INT);
        $stmt->bindValue(':km', $carro->getKm(), PDO::PARAM_INT);
        $stmt->bindValue(':tanque', (string) $carro->getTanque());
    }
}
