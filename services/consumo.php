<?php

function calcularMetricas(array $abastecimentos, $tanque): array
{
    $ordenada = array_values($abastecimentos);
    usort($ordenada, 'metricasComparar');

    $ordem = [];
    foreach ($ordenada as $item) {
        $ordem[] = [
            'odometro' => $item['odometro'] ?? null,
            'data' => $item['data'] ?? null,
        ];
    }

    $custos = [];
    foreach ($abastecimentos as $item) {
        if (!metricasCompleto($item)) {
            continue;
        }
        $custos[] = [
            'odometro' => $item['odometro'] + 0,
            'data' => $item['data'] ?? null,
            'custo' => metricasArredondar2($item['qtd_abast'] * $item['preco_litro']),
        ];
    }

    $cadeia = [];
    foreach ($ordenada as $item) {
        if (metricasCompleto($item)) {
            $cadeia[] = $item;
        }
    }

    $kmSoma = 0.0;
    $litrosSoma = 0.0;
    $custoSoma = 0.0;
    $intervalos = [];

    for ($i = 1, $n = count($cadeia); $i < $n; $i++) {
        $anterior = $cadeia[$i - 1];
        $atual = $cadeia[$i];
        if (($atual['odometro'] + 0) <= ($anterior['odometro'] + 0)) {
            continue;
        }
        if (($atual['qtd_abast'] + 0) <= 0) {
            continue;
        }
        $km = ($atual['odometro'] + 0) - ($anterior['odometro'] + 0);
        $litros = $atual['qtd_abast'] + 0;
        $kmSoma += $km;
        $litrosSoma += $litros;
        $custoSoma += $litros * ($atual['preco_litro'] + 0);
        $intervalos[] = [
            'km' => $km,
            'km_l_intervalo' => $km / $litros,
            'odometro_anterior' => $anterior['odometro'] + 0,
            'odometro_atual' => $atual['odometro'] + 0,
        ];
    }

    $kmL = count($intervalos) > 0 ? metricasArredondar2($kmSoma / $litrosSoma) : null;
    $custoKm = count($intervalos) > 0 ? metricasArredondar2($custoSoma / $kmSoma) : null;

    $autonomia = null;
    if ($kmL !== null && is_numeric($tanque) && ($tanque + 0) > 0) {
        $autonomia = metricasArredondar2(($tanque + 0) * $kmL);
    }

    return [
        'km_l' => $kmL,
        'custo_km' => $custoKm,
        'autonomia_km' => $autonomia,
        'custos' => $custos,
        'intervalos' => $intervalos,
        'ordem' => $ordem,
    ];
}

function metricasCompleto(array $item): bool
{
    return isset($item['qtd_abast'], $item['preco_litro'], $item['odometro'])
        && is_numeric($item['qtd_abast'])
        && is_numeric($item['preco_litro'])
        && is_numeric($item['odometro']);
}

function metricasComparar(array $a, array $b): int
{
    $oa = $a['odometro'] ?? null;
    $ob = $b['odometro'] ?? null;
    $na = is_numeric($oa);
    $nb = is_numeric($ob);

    if ($na && $nb && ((float) $oa != (float) $ob)) {
        return (float) $oa <=> (float) $ob;
    }
    if ($na !== $nb) {
        return $na ? -1 : 1;
    }
    if (!$na && (string) $oa !== (string) $ob) {
        return strcmp((string) $oa, (string) $ob);
    }

    return strcmp((string) ($a['data'] ?? ''), (string) ($b['data'] ?? ''));
}

function metricasArredondar2($valor): float
{
    return round((float) $valor, 2, PHP_ROUND_HALF_UP);
}
