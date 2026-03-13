<?php 
// AVISO: Arquivo 'Usuario.php' não foi encontrado. Comentado para evitar erro.
// require_once 'Usuario.php';
// require_once 'noticias.php';

class Professor extends Usuario {
    public function exibirPermissoes() {
        return "Professor pode criar e editar notícias.<br>";
    }
    public function criarNoticia($titulo, $conteudo) {
        $noticia = new Noticia($titulo, $conteudo);
        echo "<p class=\"mensagem\">Notícia criada por <strong>" . $this->getNome() . "</strong>: " . $noticia->getTitulo() . "</p>";
        return $noticia;
    }


    public function editarNoticia($noticia, $novoTitulo, $novoConteudo) {
        $noticia->setTitulo($novoTitulo);
        $noticia->setConteudo($novoConteudo);
        echo "Notícia editada por " . $this->getNome() . ": " . $noticia->getTitulo() . "<br>";
    }
}