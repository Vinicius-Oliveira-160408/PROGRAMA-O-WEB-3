<?php
require_once __DIR__ . '/../config/conexao.php';

/** Lista cidades com nome do país e do governante (JOIN) */
function listarCidades(): array
{
    $pdo = conectar();
    $sql = "SELECT ci.*, p.nm_pais, g.nm_governante
            FROM cidades ci
            LEFT JOIN paises p ON p.id_pais = ci.pais_id
            LEFT JOIN governantes g ON g.id_governante = ci.governante_id
            ORDER BY ci.nome ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Busca uma cidade pelo id */
function buscarCidade(int $id)
{
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM cidades WHERE id_cidade = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Insere uma nova cidade */
function inserirCidade(array $d): bool
{
    $pdo = conectar();
    $sql = "INSERT INTO cidades (nome, pais_id, populacao, area_km2, clima, governante_id, dt_fundacao)
            VALUES (:nome, :pais, :pop, :area, :clima, :governante, :dt_fundacao)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nome'        => $d['nome'],
        'pais'        => $d['pais_id'] !== '' ? $d['pais_id'] : null,
        'pop'         => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'        => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'clima'       => $d['clima'] !== '' ? $d['clima'] : null,
        'governante'  => $d['governante_id'] !== '' ? $d['governante_id'] : null,
        'dt_fundacao' => $d['dt_fundacao'] !== '' ? $d['dt_fundacao'] : null,
    ]);
}

/** Atualiza uma cidade existente */
function atualizarCidade(int $id, array $d): bool
{
    $pdo = conectar();
    $sql = "UPDATE cidades
               SET nome = :nome, pais_id = :pais, populacao = :pop, area_km2 = :area,
                   clima = :clima, governante_id = :governante, dt_fundacao = :dt_fundacao
             WHERE id_cidade = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nome'        => $d['nome'],
        'pais'        => $d['pais_id'] !== '' ? $d['pais_id'] : null,
        'pop'         => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'        => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'clima'       => $d['clima'] !== '' ? $d['clima'] : null,
        'governante'  => $d['governante_id'] !== '' ? $d['governante_id'] : null,
        'dt_fundacao' => $d['dt_fundacao'] !== '' ? $d['dt_fundacao'] : null,
        'id'          => $id,
    ]);
}

/** Exclui uma cidade */
function excluirCidade(int $id): bool
{
    $pdo = conectar();
    $stmt = $pdo->prepare("DELETE FROM cidades WHERE id_cidade = :id");
    return $stmt->execute(['id' => $id]);
}
