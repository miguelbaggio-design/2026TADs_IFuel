<?php
$titulo = 'IFuel';
$subtitulo = 'Controle de consumo de combustível';

$funcionalidades = [
    ['titulo' => 'Veículos',       'descricao' => 'Associe carros aos seus proprietários.',                 'link' => '#'],
    ['titulo' => 'Abastecimentos', 'descricao' => 'Registre valor por litro, litros e odômetro.',           'link' => '#'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - <?= htmlspecialchars($subtitulo) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f4f6f8; color: #222; }
        header { background: #1b7f3b; color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: #fff; text-decoration: none; margin-left: 16px; }
        nav a:hover { text-decoration: underline; }
        .hero { text-align: center; padding: 64px 24px; background: #e6f4ea; }
        .hero h2 { font-size: 2rem; margin-bottom: 12px; }
        .hero p { color: #555; max-width: 600px; margin: 0 auto; }
        main { max-width: 960px; margin: 32px auto; padding: 0 24px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        .card h3 { margin-bottom: 8px; color: #1b7f3b; }
        .card p { font-size: .95rem; color: #555; margin-bottom: 12px; }
        .card a { color: #1b7f3b; font-weight: 600; text-decoration: none; }
        footer { text-align: center; padding: 24px; color: #777; font-size: .85rem; }
    </style>
</head>
<body>
    <header>
        <h1>⛽ <?= htmlspecialchars($titulo) ?></h1>
        <nav>
            <?php foreach ($funcionalidades as $f): ?>
                <a href="<?= htmlspecialchars($f['link']) ?>"><?= htmlspecialchars($f['titulo']) ?></a>
            <?php endforeach; ?>
        </nav>
    </header>

    <section class="hero">
        <h2><?= htmlspecialchars($subtitulo) ?></h2>
        <p>Registre seus abastecimentos e descubra a média de consumo e o custo do seu veículo.</p>
    </section>

    <main>
        <div class="cards">
            <?php foreach ($funcionalidades as $f): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($f['titulo']) ?></h3>
                    <p><?= htmlspecialchars($f['descricao']) ?></p>
                    <a href="<?= htmlspecialchars($f['link']) ?>">Acessar →</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        TADS 2026 · IFSUL Campus Venâncio Aires · <?= date('Y') ?>
    </footer>
</body>
</html>
