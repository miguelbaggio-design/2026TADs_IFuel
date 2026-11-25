<?php

require_once __DIR__ . '/../models/abastecimentoModel.php';

/**
 * Acesso a dados da tabela `abastecimento`.
 * Recebe a conexão PDO pronta, para não depender de como o config.php a cria.
 */
class AbastecimentoDAO
{
    private PDO $conexao;

    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Insere o abastecimento e preenche o id gerado no próprio objeto.
     * Sem data informada, grava a data/hora atual (o formulário não tem campo de data).
     */
    public function inserir(Abastecimento $abastecimento): Abastecimento
    {
        if ($abastecimento->getData() === null) {
            $abastecimento->setData(date(Abastecimento::FORMATO_DATA));
        }

        $sql = 'INSERT INTO abastecimento (carro_id, odometro, valor_total, litros, data)
                VALUES (:carro_id, :odometro, :valor_total, :litros, :data)';

        $stmt = $this->conexao->prepare($sql);
        $this->vincularCampos($stmt, $abastecimento);
        $stmt->execute();

        $abastecimento->setId((int) $this->conexao->lastInsertId());
        return $abastecimento;
    }

    public function buscarPorId(int $id): ?Abastecimento
    {
        $stmt = $this->conexao->prepare('SELECT * FROM abastecimento WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $linha ? Abastecimento::fromArray($linha) : null;
    }

    /**
     * Histórico de um carro, do mais antigo para o mais recente
     * (pelo odômetro e, em empate, pela data).
     *
     * @return Abastecimento[]
     */
    public function listarPorCarro(int $carroId): array
    {
        $stmt = $this->conexao->prepare(
            'SELECT * FROM abastecimento WHERE carro_id = :carro_id ORDER BY odometro, data'
        );
        $stmt->bindValue(':carro_id', $carroId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->montarLista($stmt);
    }

    /**
     * Todos os abastecimentos dos carros de um usuário, do mais recente para o mais antigo.
     *
     * @return Abastecimento[]
     */
    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->conexao->prepare(
            'SELECT a.* FROM abastecimento a
               JOIN carro c ON c.id = a.carro_id
              WHERE c.usuario_id = :usuario_id
              ORDER BY a.data DESC, a.id DESC'
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->montarLista($stmt);
    }

    /**
     * Retorna false se nenhuma linha mudou. No MySQL isso também acontece
     * quando o id existe mas os valores enviados são iguais aos gravados.
     */
    public function atualizar(Abastecimento $abastecimento): bool
    {
        if ($abastecimento->getId() === null) {
            throw new InvalidArgumentException('Não é possível atualizar um abastecimento sem id.');
        }
        if ($abastecimento->getData() === null) {
            throw new InvalidArgumentException('Informe a data do abastecimento ao atualizar.');
        }

        $sql = 'UPDATE abastecimento
                   SET carro_id = :carro_id, odometro = :odometro, valor_total = :valor_total,
                       litros = :litros, data = :data
                 WHERE id = :id';

        $stmt = $this->conexao->prepare($sql);
        $this->vincularCampos($stmt, $abastecimento);
        $stmt->bindValue(':id', $abastecimento->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->conexao->prepare('DELETE FROM abastecimento WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @return Abastecimento[]
     */
    private function montarLista(PDOStatement $stmt): array
    {
        $lista = [];
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = Abastecimento::fromArray($linha);
        }
        return $lista;
    }

    private function vincularCampos(PDOStatement $stmt, Abastecimento $abastecimento): void
    {
        $litros = $abastecimento->getLitros();
        $stmt->bindValue(':carro_id', $abastecimento->getCarroId(), PDO::PARAM_INT);
        $stmt->bindValue(':odometro', $abastecimento->getOdometro(), PDO::PARAM_INT);
        $stmt->bindValue(':valor_total', (string) $abastecimento->getValorTotal());
        $stmt->bindValue(':litros', $litros === null ? null : (string) $litros, $litros === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':data', $abastecimento->getData());
    }
}
