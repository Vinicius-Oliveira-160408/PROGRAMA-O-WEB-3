<?php
require_once __DIR__ . '/../config/conexao.php';

/** Lista todos os governantes */
function listarGovernantes(): array
{
    $pdo = conectar();
    $sql = "SELECT * FROM governantes ORDER BY nm_governante ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Busca um governante pelo id */
function buscarGovernante(int $id)
{
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM governantes WHERE id_governante = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Insere um novo governante */
function inserirGovernante(array $d): bool
{
    $pdo = conectar();
    $sql = "INSERT INTO governantes
                (nm_governante, partido_politico, dt_nascimento, idade, dt_inicio_mandato, dt_final_mandato)
            VALUES (:nm, :partido, :dt_nasc, :idade, :dt_ini, :dt_fim)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'      => $d['nm_governante'],
        'partido' => $d['partido_politico'] !== '' ? $d['partido_politico'] : null,
        'dt_nasc' => $d['dt_nascimento'] !== '' ? $d['dt_nascimento'] : null,
        'idade'   => $d['idade'] !== '' ? $d['idade'] : null,
        'dt_ini'  => $d['dt_inicio_mandato'] !== '' ? $d['dt_inicio_mandato'] : null,
        'dt_fim'  => $d['dt_final_mandato'] !== '' ? $d['dt_final_mandato'] : null,
    ]);
}

/** Atualiza um governante existente */
function atualizarGovernante(int $id, array $d): bool
{
    $pdo = conectar();
    $sql = "UPDATE governantes
               SET nm_governante = :nm, partido_politico = :partido, dt_nascimento = :dt_nasc,
                   idade = :idade, dt_inicio_mandato = :dt_ini, dt_final_mandato = :dt_fim
             WHERE id_governante = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nm'      => $d['nm_governante'],
        'partido' => $d['partido_politico'] !== '' ? $d['partido_politico'] : null,
        'dt_nasc' => $d['dt_nascimento'] !== '' ? $d['dt_nascimento'] : null,
        'idade'   => $d['idade'] !== '' ? $d['idade'] : null,
        'dt_ini'  => $d['dt_inicio_mandato'] !== '' ? $d['dt_inicio_mandato'] : null,
        'dt_fim'  => $d['dt_final_mandato'] !== '' ? $d['dt_final_mandato'] : null,
        'id'      => $id,
    ]);
}

/** Exclui um governante. Lança PDOException (23000) se houver países/cidades vinculados */
function excluirGovernante(int $id): bool
{
    $pdo = conectar();
    $stmt = $pdo->prepare("DELETE FROM governantes WHERE id_governante = :id");
    return $stmt->execute(['id' => $id]);
}
