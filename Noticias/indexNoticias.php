<?php
require_once 'Usuario.php';
require_once 'professor.php'; 
require_once 'aluno.php';
require_once 'admin.php';
require_once 'noticias.php';
require_once 'comentarios.php';

$professor = new Professor("Carlos", "carlos@escola.com");
$admin = new Admin("Mariana", "admin@escola.com");
$aluno = new Aluno("Lucca", "lucca@escola.com");


$noticia = $professor->criarNoticia(
    "Feira de Tecnologia 2026",
    "A escola realizará a feira no próximo mês."
);

$noticia->exibirDetalhes();
echo "<hr>";

$admin->aprovarNoticia($noticia);

$aluno->comentarNoticia($noticia, "Muito legal! Vou participar.");

echo "<hr>";
$noticia->exibirDetalhes();
?>