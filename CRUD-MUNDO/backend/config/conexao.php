<?php
/**
 * Conexão com o banco de dados bd_mundo via PDO.
 * Ajuste $usuario e $senha se o seu MySQL do XAMPP exigir.
 */

function conectar()
{
    $host    = 'localhost';
    $dbname  = 'bd_mundo';
    $usuario = 'root';
    $senha   = '';

    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $usuario,
            $senha
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
    }
}
