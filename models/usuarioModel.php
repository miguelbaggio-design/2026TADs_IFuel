<?php

class Usuario
{
    private ?int $id;
    private string $nome;
    private string $email;
    private string $senhaHash;

    public function __construct(
        string $nome = '',
        string $email = '',
        string $senhaHash = '',
        ?int $id = null
    ) {
        $this->nome = trim($nome);
        $this->email = self::normalizarEmail($email);
        $this->senhaHash = $senhaHash;
        $this->id = $id;
    }

    /**
     * Cria um Usuario a partir de uma linha do banco.
     * Para dados de formulário, use o construtor + definirSenha().
     */
    public static function fromArray(array $dados): Usuario
    {
        return new Usuario(
            (string) ($dados['nome'] ?? ''),
            (string) ($dados['email'] ?? ''),
            (string) ($dados['senha_hash'] ?? ''),
            isset($dados['id']) && $dados['id'] !== '' ? (int) $dados['id'] : null
        );
    }

    /**
     * Dados públicos do usuário. O hash da senha fica de fora de propósito.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
        ];
    }

    /**
     * Valida nome e e-mail. A senha é validada em validarSenha(),
     * porque depois de gravada só existe o hash.
     */
    public function validar(): array
    {
        $erros = [];

        if ($this->nome === '') {
            $erros[] = 'Informe o nome.';
        } elseif (!preg_match('/^.{1,100}$/us', $this->nome)) {
            $erros[] = 'O nome deve ter no máximo 100 caracteres.';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif (strlen($this->email) > 150) {
            $erros[] = 'O e-mail deve ter no máximo 150 caracteres.';
        }
        if ($this->senhaHash === '') {
            $erros[] = 'Defina uma senha.';
        }

        return $erros;
    }

    /**
     * Regras da senha em texto puro (formulário de cadastro).
     */
    public static function validarSenha(string $senha, string $confirmacao): array
    {
        $erros = [];

        if (strlen($senha) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        if ($senha !== $confirmacao) {
            $erros[] = 'As senhas não conferem.';
        }

        return $erros;
    }

    public function definirSenha(string $senha): void
    {
        $this->senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    }

    public function verificarSenha(string $senha): bool
    {
        return $this->senhaHash !== '' && password_verify($senha, $this->senhaHash);
    }

    public static function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = trim($nome);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = self::normalizarEmail($email);
    }

    public function getSenhaHash(): string
    {
        return $this->senhaHash;
    }
}
