<?php
$basePath = '../';
$titulo = 'Login';
$subtitulo = 'Acesse sua conta';

require_once __DIR__ . '/../components/header.php';
?>

<main>
    <div class="auth-container">
        <div class="section-card">
            <h2>
                <i class="fa-solid fa-user"></i>
                Entrar no IFuel
            </h2>

            <p class="auth-description">
                Informe seus dados para acessar sua conta.
            </p>

            <form action="#" method="POST" class="auth-form">

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
                        placeholder="Digite sua senha"
                        required
                    >
                </div>

                <button type="submit" class="auth-button">
                    Entrar
                </button>

            </form>

            <p class="auth-link">
                Ainda não possui uma conta?
                <a href="formCadastro.php">Cadastre-se</a>
            </p>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../components/footer.php';
?>
