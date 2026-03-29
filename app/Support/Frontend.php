<?php

namespace App\Support;

class Frontend
{
    public static function renderLanding(): string
    {
        $csrfToken = \App\Core\Csrf::generateToken();
        
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FETEL - Sistema de Gestão de Biblioteca</title>
    <script>window.__CSRF_TOKEN = '%CSRF_TOKEN%';</script>
    <link rel="stylesheet" href="/TCC-etec/public/dist/css/main-aaYBaw7H.css" />
</head>
<body>
    <div id="root"></div>
    <script type="module" src="/TCC-etec/public/dist/js/main-CvgYWSwA.js"></script>
</body>
</html>
HTML;

        return str_replace('%CSRF_TOKEN%', $csrfToken, $html);
    }
}
