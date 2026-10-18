<?php
$basePath = '';
$titulo = 'IFuel';
$subtitulo = 'Controle de consumo de combustível';

$funcionalidades = [
    [
        'titulo' => 'Veículos',
        'descricao' => 'Cadastre e gerencie os veículos da sua frota com controle de modelo, ano e quilometragem.',
        'link' => 'views/formCarro.php',
        'imagem' => 'assets/chave-do-carro.png',
        'botao' => 'Acessar'
    ],
    [
        'titulo' => 'Abastecimentos',
        'descricao' => 'Registre seus abastecimentos, controle o odômetro e calcule gastos com combustível.',
        'link' => 'views/formAbastecimento.php',
        'imagem' => 'assets/abastecendo-carro.png',
        'botao' => 'Acessar'
    ],
];

require_once __DIR__ . '/components/header.php';
?>

    <section class="hero">
        <h2><?= htmlspecialchars($subtitulo) ?></h2>
        <p>Registre seus abastecimentos e descubra a média de consumo e o custo do seu veículo.</p>
    </section>

    <main>
        <div class="cards-grid">
            <?php foreach ($funcionalidades as $f): ?>
                <div class="card-modern">
                    <div class="card-modern-content">
                        <div>
                            <h3><?= htmlspecialchars($f['titulo']) ?></h3>
                            <p><?= htmlspecialchars($f['descricao']) ?></p>
                        </div>
                        <a href="<?= htmlspecialchars($f['link']) ?>" class="card-modern-action">
                            <span><?= htmlspecialchars($f['botao']) ?></span>
                            <span class="btn-arrow-circle">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                    <div class="card-modern-image-wrapper">
                        <img src="<?= htmlspecialchars($f['imagem']) ?>" alt="<?= htmlspecialchars($f['titulo']) ?>" class="card-modern-image">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

<?php
require_once __DIR__ . '/components/footer.php';
?>