<?php
$basePath = '../';
$titulo = 'Registro de Abastecimento';
$subtitulo = 'Controle e histórico de abastecimentos';

// Mock de carros para preencher o dropdown (sem lógica de banco por enquanto)
$carros = [
    ['id' => 1, 'modelo' => 'Chevrolet Onix 1.0 Turbo', 'ano' => 2022],
    ['id' => 2, 'modelo' => 'Volkswagen Gol 1.6 MSI',   'ano' => 2019],
    ['id' => 3, 'modelo' => 'Fiat Strada 1.3 Firefly',  'ano' => 2023],
];

// Dados ilustrativos para exibição do histórico de abastecimentos
$abastecimentosExemplo = [
    [
        'id' => 1,
        'carro' => 'Chevrolet Onix 1.0 Turbo',
        'data' => '17/10/2026',
        'km' => 34500,
        'litros' => 42.50,
        'valor_total' => 245.00
    ],
    [
        'id' => 2,
        'carro' => 'Volkswagen Gol 1.6 MSI',
        'data' => '15/10/2026',
        'km' => 78200,
        'litros' => 38.00,
        'valor_total' => 218.50
    ],
    [
        'id' => 3,
        'carro' => 'Fiat Strada 1.3 Firefly',
        'data' => '12/10/2026',
        'km' => 19800,
        'litros' => 45.20,
        'valor_total' => 260.00
    ]
];

require_once __DIR__ . '/../components/header.php';
?>

<main>
    <!-- Formulário para Registrar Abastecimento -->
    <section class="section-card">
        <h2><i class="fa-solid fa-gas-pump"></i> Novo Abastecimento</h2>
        <form action="#" method="POST">
            <div class="form-grid-flex">
                <!-- Dropdown de Seleção do Carro -->
                <div class="form-group" style="min-width: 220px;">
                    <label for="carro_id">Selecione o Veículo</label>
                    <select id="carro_id" name="carro_id" required>
                        <option value="">Selecione um carro...</option>
                        <?php foreach ($carros as $c): ?>
                            <option value="<?= htmlspecialchars($c['id']) ?>">
                                <?= htmlspecialchars($c['modelo']) ?> (<?= htmlspecialchars($c['ano']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nova Quilometragem -->
                <div class="form-group">
                    <label for="km">Nova Quilometragem (KM)</label>
                    <input type="number" id="km" name="km" min="0" step="1" placeholder="Ex: 35200" required>
                </div>

                <!-- Quanto gastou de gasolina -->
                <div class="form-group">
                    <label for="valor_gasto">Valor Gasto (R$)</label>
                    <input type="number" id="valor_gasto" name="valor_gasto" step="0.01" min="0" placeholder="Ex: 200,00" required>
                </div>

                <!-- Quantidade de Litros (Opcional para cálculo de consumo) -->
                <div class="form-group">
                    <label for="litros">Litros Abastecidos</label>
                    <input type="number" id="litros" name="litros" step="0.01" min="0" placeholder="Ex: 35.50">
                </div>

                <!-- Botão de Ação -->
                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-plus"></i> Registrar
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Histórico de Abastecimentos Abaixo -->
    <section class="section-card">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> Histórico de Abastecimentos</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;"># ID</th>
                        <th>Veículo</th>
                        <th style="width: 120px;">Data</th>
                        <th style="width: 150px;">Odômetro</th>
                        <th style="width: 110px;">Litros</th>
                        <th style="width: 150px;">Total Gasto</th>
                        <th style="width: 180px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($abastecimentosExemplo)): ?>
                        <?php foreach ($abastecimentosExemplo as $abast): ?>
                            <tr>
                                <td><?= htmlspecialchars($abast['id']) ?></td>
                                <td><strong><?= htmlspecialchars($abast['carro']) ?></strong></td>
                                <td><?= htmlspecialchars($abast['data']) ?></td>
                                <td><span class="badge-km"><?= number_format($abast['km'], 0, ',', '.') ?> km</span></td>
                                <td><?= number_format($abast['litros'], 2, ',', '.') ?> L</td>
                                <td><strong style="color: #1b7f3b;">R$ <?= number_format($abast['valor_total'], 2, ',', '.') ?></strong></td>
                                <td style="text-align: center;">
                                    <div class="actions-group">
                                        <a href="#" class="btn-sm btn-edit" title="Editar Abastecimento">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>
                                        <a href="#" class="btn-sm btn-delete" title="Excluir Abastecimento">
                                            <i class="fa-solid fa-trash-can"></i> Excluir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #888; padding: 24px;">
                                Nenhum abastecimento registrado até o momento.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/../components/footer.php';
?>

