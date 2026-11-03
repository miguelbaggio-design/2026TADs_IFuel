<?php
$basePath = $basePath ?? '';
$titulo = $titulo ?? 'IFuel';
$subtitulo = $subtitulo ?? 'Controle de consumo de combustível';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>styles.css">
    <title><?= htmlspecialchars($titulo) ?> - <?= htmlspecialchars($subtitulo) ?></title>
</head>
<body>
    <header>
        <a href="<?= $basePath ?>index.php" class="logo-link">
            <img src="<?= $basePath ?>assets/ifuel-logo.png" alt="IFuel Logo" class="header-logo">
        </a>
        <nav>
            <a href="<?= $basePath ?>index.php"><i class="fa-solid fa-house"></i> Início</a>
            <a href="<?= $basePath ?>views/formCarro.php"><i class="fa-solid fa-car"></i> Veículos</a>
            <a href="<?= $basePath ?>views/formAbastecimento.php"><i class="fa-solid fa-gas-pump"></i> Abastecimentos</a>
            <a href="<?= $basePath ?>views/formLogin.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="<?= $basePath ?>views/formCadastro.php"><i class="fa-solid fa-user-plus"></i> Cadastro</a>
        </nav>
    </header>
