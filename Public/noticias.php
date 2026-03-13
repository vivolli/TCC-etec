<?php

require_once __DIR__ . '/../Core/autenticacao.php';

iniciar_sessao_segura();

// Nóticas disponível para todos
require __DIR__ . '/../app/View/noticias.html';