<?php
$basePath = '../';
$titulo = 'Cadastro de Veículos';
$subtitulo = 'Gerenciamento de veículos';

// Dados ilustrativos para visualização da estrutura
$carrosExemplo = [
    ['id' => 1, 'modelo' => 'Chevrolet Onix 1.0 Turbo', 'ano' => 2022, 'km' => 34500],
    ['id' => 2, 'modelo' => 'Volkswagen Gol 1.6 MSI',   'ano' => 2019, 'km' => 78200],
    ['id' => 3, 'modelo' => 'Fiat Strada 1.3 Firefly',  'ano' => 2023, 'km' => 19800],
];

require_once __DIR__ . '/../components/header.php';
?>

<main>
    <!-- Formulário para Adicionar Carro -->
    <section class="section-card">
        <h2><i class="fa-solid fa-car-side"></i> Novo Veículo</h2>
        <form action="#" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="modelo">Modelo do Carro</label>
                    <input type="text" id="modelo" name="modelo" placeholder="Ex: Onix 1.0, Gol 1.6..." required>
                </div>

                <div class="form-group">
                    <label for="ano">Ano</label>
                    <input type="number" id="ano" name="ano" min="1950" max="<?= date('Y') + 1 ?>" placeholder="Ex: <?= date('Y') ?>" required>
                </div>

                <div class="form-group">
                    <label for="km">Quilometragem (KM)</label>
                    <input type="number" id="km" name="km" min="0" step="1" placeholder="Ex: 50000" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-plus"></i> Adicionar Carro
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Listagem de Carros Existentes -->
    <section class="section-card">
        <h2><i class="fa-solid fa-list-check"></i> Veículos Cadastrados</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;"># ID</th>
                        <th>Modelo</th>
                        <th style="width: 120px;">Ano</th>
                        <th style="width: 160px;">Quilometragem</th>
                        <th style="width: 180px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($carrosExemplo)): ?>
                        <?php foreach ($carrosExemplo as $carro): ?>
                            <tr>
                                <td><?= htmlspecialchars($carro['id']) ?></td>
                                <td><strong><?= htmlspecialchars($carro['modelo']) ?></strong></td>
                                <td><?= htmlspecialchars($carro['ano']) ?></td>
                                <td><span class="badge-km"><?= number_format($carro['km'], 0, ',', '.') ?> km</span></td>
                                <td style="text-align: center;">
                                    <div class="actions-group">
                                        <a href="#" class="btn-sm btn-edit" title="Editar Veículo">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar
                                        </a>
                                        <a href="#" class="btn-sm btn-delete" title="Excluir Veículo">
                                            <i class="fa-solid fa-trash-can"></i> Excluir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888; padding: 24px;">
                                Nenhum veículo cadastrado até o momento.
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