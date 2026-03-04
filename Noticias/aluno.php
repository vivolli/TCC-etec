<?php 
require_once 'Usuario.php';
require_once 'comentarios.php';

class Aluno extends Usuario {
    public function exibirPermissoes() {
        return "Aluno pode comentar notícias.<br>";
    }
    public function comentarNoticia($noticia, $conteudo) {
        $comentario = new Comentario($this, $conteudo);
        $noticia->adicionarComentario($comentario);
      
        echo "<p class=\"mensagem\">Comentário adicionado por <strong>" . $this->getNome() . "</strong>: " . $comentario->getConteudo() . "</p>";
    }
}