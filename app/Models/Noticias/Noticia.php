<?php
class Noticia {
    private $titulo;
    private $conteudo;
    private $comentarios = [];
    private $publicada = false;

    public function __construct($titulo, $conteudo) {
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
    }
    public function isPublicada() {
        return $this->publicada;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getConteudo() {
        return $this->conteudo;
    }

    public function adicionarComentario($comentario) {
        $this->comentarios[] = $comentario;
    }

    public function publicar() {
        $this->publicada = true;
        echo "<p class=\"mensagem\">Notícia '<strong>" . $this->titulo . "</strong>' publicada com sucesso.</p>";
    }

    public function exibirDetalhes() {
        echo "<article class='noticia'>";
        echo "<h2>" . $this->titulo . "</h2>";
        echo "<p>" . $this->conteudo . "</p>";

        if (!empty($this->comentarios)) {
            echo "<div class='comentarios'>";
            echo "<h3>Comentários</h3>";
            foreach ($this->comentarios as $comentario) {
                echo "<div class='comentario'><span class='autor'>" . $comentario->getAutor() . "</span>: " . $comentario->getConteudo() . "</div>";
            }
            echo "</div>";
        }

        echo "</article>";
    }
}
