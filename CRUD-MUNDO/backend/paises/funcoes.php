<?php
require_once __DIR__ . '/../config/conexao.php';

/** Lista países com nome do continente e do governante (JOIN) */
function listarPaises(): array
{
    $pdo = conectar();
    $sql = "SELECT p.*,
                   c.nm_continente,
                   g.nm_governante,
                   (SELECT COUNT(*) FROM cidades ci WHERE ci.pais_id = p.id_pais) AS qtd_cidades
            FROM paises p
            LEFT JOIN continentes c ON c.id_continente = p.continente_id
            LEFT JOIN governantes g ON g.id_governante = p.governante_id
            ORDER BY p.nm_pais ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Busca um país pelo id */
function buscarPais(int $id)
{
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM paises WHERE id_pais = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Cidade mais populosa de cada país (estatística extra) */
function cidadeMaisPopulosaPorPais(): array
{
    $pdo = conectar();
    $sql = "SELECT p.id_pais, p.nm_pais, ci.nome AS cidade, ci.populacao
            FROM paises p
            JOIN cidades ci ON ci.pais_id = p.id_pais
            WHERE ci.populacao = (
                SELECT MAX(ci2.populacao) FROM cidades ci2 WHERE ci2.pais_id = p.id_pais
            )
            ORDER BY p.nm_pais ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Insere um novo país */
function inserirPais(array $d): bool
{
    $pdo = conectar();
    $sql = "INSERT INTO paises
                (nm_pais, continente_id, populacao, area_km2, idioma, governante_id, clima, regime_politico, moeda)
            VALUES (:nm, :continente, :pop, :area, :idioma, :governante, :clima, :regime, :moeda)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'         => $d['nm_pais'],
        'continente' => $d['continente_id'] !== '' ? $d['continente_id'] : null,
        'pop'        => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'       => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'idioma'     => $d['idioma'] !== '' ? $d['idioma'] : null,
        'governante' => $d['governante_id'] !== '' ? $d['governante_id'] : null,
        'clima'      => $d['clima'] !== '' ? $d['clima'] : null,
        'regime'     => $d['regime_politico'] !== '' ? $d['regime_politico'] : null,
        'moeda'      => $d['moeda'] !== '' ? $d['moeda'] : null,
    ]);
}

/** Atualiza um país existente */
function atualizarPais(int $id, array $d): bool
{
    $pdo = conectar();
    $sql = "UPDATE paises
               SET nm_pais = :nm, continente_id = :continente, populacao = :pop, area_km2 = :area,
                   idioma = :idioma, governante_id = :governante, clima = :clima,
                   regime_politico = :regime, moeda = :moeda
             WHERE id_pais = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'         => $d['nm_pais'],
        'continente' => $d['continente_id'] !== '' ? $d['continente_id'] : null,
        'pop'        => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'       => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'idioma'     => $d['idioma'] !== '' ? $d['idioma'] : null,
        'governante' => $d['governante_id'] !== '' ? $d['governante_id'] : null,
        'clima'      => $d['clima'] !== '' ? $d['clima'] : null,
        'regime'     => $d['regime_politico'] !== '' ? $d['regime_politico'] : null,
        'moeda'      => $d['moeda'] !== '' ? $d['moeda'] : null,
        'id'         => $id,
    ]);
}

/** Exclui um país. Lança PDOException (23000) se houver cidades vinculadas */
function excluirPais(int $id): bool
{
    $pdo = conectar();
    $stmt = $pdo->prepare("DELETE FROM paises WHERE id_pais = :id");
    return $stmt->execute(['id' => $id]);
}
