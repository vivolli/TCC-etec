<?php
// Centraliza inclusão dos assets gerados pelo frontend build (TS + Tailwind)
$frontendBase = '/TCC-etec/app/public/frontend';
?>
<!-- Frontend bundle CSS -->
<link rel="stylesheet" href="<?= $frontendBase ?>/style.css">
<!-- Frontend bundle JS -->
<script type="module" src="<?= $frontendBase ?>/main.js" defer></script>
