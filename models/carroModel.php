<?php

class Carro
{
    private ?int $id;
    private ?int $usuarioId;
    private string $modelo;
    private int $ano;
    private int $km;
    private float $tanque;

    public function __construct(
        string $modelo = '',
        int $ano = 0,
        int $km = 0,
        float $tanque = 0.0,
        ?int $usuarioId = null,
        ?int $id = null
    ) {
        $this->modelo = trim($modelo);
        $this->ano = $ano;
        $this->km = $km;
        $this->tanque = $tanque;
        $this->usuarioId = $usuarioId;
        $this->id = $id;
    }

    /**
     * Cria um Carro a partir de um array (linha do banco ou $_POST).
     */
    public static function fromArray(array $dados): Carro
    {
        return new Carro(
            (string) ($dados['modelo'] ?? ''),
            (int) ($dados['ano'] ?? 0),
            (int) ($dados['km'] ?? 0),
            (float) ($dados['tanque'] ?? 0),
            isset($dados['usuario_id']) && $dados['usuario_id'] !== '' ? (int) $dados['usuario_id'] : null,
            isset($dados['id']) && $dados['id'] !== '' ? (int) $dados['id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuarioId,
            'modelo' => $this->modelo,
            'ano' => $this->ano,
            'km' => $this->km,
            'tanque' => $this->tanque,
        ];
    }

    /**
     * Retorna a lista de erros de validação (vazia se o carro for válido).
     */
    public function validar(): array
    {
        $erros = [];
        $anoMaximo = (int) date('Y') + 1;

        if ($this->modelo === '') {
            $erros[] = 'Informe o modelo do carro.';
        }
        if ($this->ano < 1950 || $this->ano > $anoMaximo) {
            $erros[] = "O ano deve estar entre 1950 e {$anoMaximo}.";
        }
        if ($this->km < 0) {
            $erros[] = 'A quilometragem não pode ser negativa.';
        }
        if ($this->tanque <= 0) {
            $erros[] = 'A capacidade do tanque deve ser maior que zero.';
        }

        return $erros;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsuarioId(): ?int
    {
        return $this->usuarioId;
    }

    public function setUsuarioId(?int $usuarioId): void
    {
        $this->usuarioId = $usuarioId;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function setModelo(string $modelo): void
    {
        $this->modelo = trim($modelo);
    }

    public function getAno(): int
    {
        return $this->ano;
    }

    public function setAno(int $ano): void
    {
        $this->ano = $ano;
    }

    public function getKm(): int
    {
        return $this->km;
    }

    public function setKm(int $km): void
    {
        $this->km = $km;
    }

    public function getTanque(): float
    {
        return $this->tanque;
    }

    public function setTanque(float $tanque): void
    {
        $this->tanque = $tanque;
    }
}
