<?php

require_once __DIR__ . '/../services/consumo.php';

function mapearAbastecimento(array $entrada): array
{
    $litros = campoNumerico($entrada, 'litros');
    $valor = campoNumerico($entrada, 'valor_gasto');

    $precoLitro = null;
    if ($litros !== null && $litros > 0 && $valor !== null) {
        $precoLitro = $valor / $litros;
    }

    return [
        'odometro' => campoNumerico($entrada, 'km'),
        'qtd_abast' => $litros,
        'preco_litro' => $precoLitro,
        'data' => $entrada['data'] ?? null,
    ];
}

function metricasDoFormulario(array $abastecimentos, $tanque): array
{
    $normalizados = [];
    foreach ($abastecimentos as $item) {
        $normalizados[] = mapearAbastecimento($item);
    }

    return calcularMetricas($normalizados, $tanque);
}

function campoNumerico(array $entrada, string $campo)
{
    if (!isset($entrada[$campo]) || !is_numeric($entrada[$campo])) {
        return null;
    }

    return $entrada[$campo] + 0;
}
