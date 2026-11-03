
<?php
$basePath = '../';
$titulo = 'Cadastro';
$subtitulo = 'Crie sua conta';

require_once __DIR__ . '/../components/header.php';
?>

<main>
    <div class="auth-container">
        <div class="section-card">
            <h2>
                <i class="fa-solid fa-user-plus"></i>
                Criar conta
            </h2>

            <p class="auth-description">
                Preencha os campos abaixo para se cadastrar.
            </p>

            <form action="#" method="POST" class="auth-form">

                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Digite seu nome"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Crie uma senha"
                        minlength="6"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirmarSenha">Confirmar senha</label>
                    <input
                        type="password"
                        id="confirmarSenha"
                        name="confirmarSenha"
                        placeholder="Digite a senha novamente"
                        minlength="6"
                        required
                    >
                </div>

                <button type="submit" class="auth-button">
                    Cadastrar
                </button>

            </form>

            <p class="auth-link">
                Já possui uma conta?
                <a href="formLogin.php">Faça login</a>
            </p>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../components/footer.php';
?>
