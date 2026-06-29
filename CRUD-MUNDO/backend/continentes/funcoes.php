<?php
require_once __DIR__ . '/../config/conexao.php';

/** Lista todos os continentes, com a contagem real de países cadastrados */
function listarContinentes(): array
{
    $pdo = conectar();
    $sql = "SELECT c.*,
                   (SELECT COUNT(*) FROM paises p WHERE p.continente_id = c.id_continente) AS qtd_paises_cadastrados
            FROM continentes c
            ORDER BY c.nm_continente ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Busca um continente pelo id */
function buscarContinente(int $id)
{
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM continentes WHERE id_continente = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Insere um novo continente */
function inserirContinente(array $d): bool
{
    $pdo = conectar();
    $sql = "INSERT INTO continentes (nm_continente, populacao, area_km2, total_paises)
            VALUES (:nm, :pop, :area, :total)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'    => $d['nm_continente'],
        'pop'   => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'  => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'total' => $d['total_paises'] !== '' ? $d['total_paises'] : null,
    ]);
}

/** Atualiza um continente existente */
function atualizarContinente(int $id, array $d): bool
{
    $pdo = conectar();
    $sql = "UPDATE continentes
               SET nm_continente = :nm, populacao = :pop, area_km2 = :area, total_paises = :total
             WHERE id_continente = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'    => $d['nm_continente'],
        'pop'   => $d['populacao'] !== '' ? $d['populacao'] : null,
        'area'  => $d['area_km2'] !== '' ? $d['area_km2'] : null,
        'total' => $d['total_paises'] !== '' ? $d['total_paises'] : null,
        'id'    => $id,
    ]);
}

/** Total de cidades cadastradas por continente (estatística extra) */
function cidadesPorContinente(): array
{
    $pdo = conectar();
    $sql = "SELECT c.id_continente, c.nm_continente,
                   COUNT(ci.id_cidade) AS qtd_cidades
            FROM continentes c
            LEFT JOIN paises p ON p.continente_id = c.id_continente
            LEFT JOIN cidades ci ON ci.pais_id = p.id_pais
            GROUP BY c.id_continente, c.nm_continente
            ORDER BY c.nm_continente ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Exclui um continente. Lança PDOException (23000) se houver países vinculados */
function excluirContinente(int $id): bool
{
    $pdo = conectar();
    $stmt = $pdo->prepare("DELETE FROM continentes WHERE id_continente = :id");
    return $stmt->execute(['id' => $id]);
}
