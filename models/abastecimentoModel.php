<?php

class Abastecimento
{
    public const FORMATO_DATA = 'Y-m-d H:i:s';

    private ?int $id;
    private int $carroId;
    private int $odometro;
    private float $valorTotal;
    private ?float $litros;
    private ?string $data;

    /**
     * $data no formato 'Y-m-d H:i:s'. Se ficar null, o DAO grava a data atual.
     */
    public function __construct(
        int $carroId = 0,
        int $odometro = 0,
        float $valorTotal = 0.0,
        ?float $litros = null,
        ?string $data = null,
        ?int $id = null
    ) {
        $this->carroId = $carroId;
        $this->odometro = $odometro;
        $this->valorTotal = $valorTotal;
        $this->litros = $litros;
        $this->data = $data;
        $this->id = $id;
    }

    /**
     * Cria um Abastecimento a partir de uma linha do banco ou do $_POST.
     * Aceita os nomes do formulário (km, valor_gasto) e os da tabela (odometro, valor_total).
     */
    public static function fromArray(array $dados): Abastecimento
    {
        $odometro = $dados['odometro'] ?? $dados['km'] ?? 0;
        $valor = $dados['valor_total'] ?? $dados['valor_gasto'] ?? 0;
        $litros = $dados['litros'] ?? null;
        $data = $dados['data'] ?? null;

        return new Abastecimento(
            (int) ($dados['carro_id'] ?? 0),
            (int) $odometro,
            (float) $valor,
            $litros === null || $litros === '' ? null : (float) $litros,
            $data === null || $data === '' ? null : (string) $data,
            isset($dados['id']) && $dados['id'] !== '' ? (int) $dados['id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'carro_id' => $this->carroId,
            'odometro' => $this->odometro,
            'valor_total' => $this->valorTotal,
            'litros' => $this->litros,
            'data' => $this->data,
        ];
    }

    /**
     * Formato esperado por calcularMetricas() em services/consumo.php.
     */
    public function paraMetricas(): array
    {
        return [
            'odometro' => $this->odometro,
            'qtd_abast' => $this->litros,
            'preco_litro' => $this->getPrecoLitro(),
            'data' => $this->data,
        ];
    }

    public function validar(): array
    {
        $erros = [];

        if ($this->carroId <= 0) {
            $erros[] = 'Selecione o veículo.';
        }
        if ($this->odometro < 0) {
            $erros[] = 'A quilometragem não pode ser negativa.';
        }
        if ($this->valorTotal <= 0) {
            $erros[] = 'O valor gasto deve ser maior que zero.';
        }
        if ($this->litros !== null && $this->litros <= 0) {
            $erros[] = 'Os litros abastecidos devem ser maiores que zero.';
        }
        if ($this->data !== null && !self::dataValida($this->data)) {
            $erros[] = 'Data inválida.';
        }

        return $erros;
    }

    /**
     * Calculado na hora (valor ÷ litros); não é gravado no banco.
     */
    public function getPrecoLitro(): ?float
    {
        if ($this->litros === null || $this->litros <= 0) {
            return null;
        }
        return $this->valorTotal / $this->litros;
    }

    private static function dataValida(string $data): bool
    {
        $convertida = DateTime::createFromFormat(self::FORMATO_DATA, $data);
        return $convertida !== false && $convertida->format(self::FORMATO_DATA) === $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCarroId(): int
    {
        return $this->carroId;
    }

    public function setCarroId(int $carroId): void
    {
        $this->carroId = $carroId;
    }

    public function getOdometro(): int
    {
        return $this->odometro;
    }

    public function setOdometro(int $odometro): void
    {
        $this->odometro = $odometro;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal): void
    {
        $this->valorTotal = $valorTotal;
    }

    public function getLitros(): ?float
    {
        return $this->litros;
    }

    public function setLitros(?float $litros): void
    {
        $this->litros = $litros;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): void
    {
        $this->data = $data;
    }
}
