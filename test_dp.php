<?php
try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=tcc-etec;charset=utf8mb4",
        "root",
        ""
    );

    echo "Banco conectado com sucesso ✅";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}