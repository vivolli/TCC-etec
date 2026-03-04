<?php 
require_once 'Usuario.php';

class admin extends Usuario {
    public function exibirPermissoes() {
        return "Admin pode aprovar e excluir notícias.<br>";
    }
    public function aprovarNoticia($noticia) {
        if ($noticia->isPublicada()) {
            echo "<p class=\"mensagem\">Notícia já está publicada.</p>";
        } else {
            $noticia->publicar();
            echo "<p class=\"mensagem\">Notícia aprovada por <strong>" . $this->getNome() . "</strong>.</p>";
        }
    }
}