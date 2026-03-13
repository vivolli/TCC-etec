<?php
// shim: include canonical header moved to app/php/header.php
// This file keeps backward compatibility for templates that include /php/header.php
$canonical = __DIR__ . '/../app/php/header.php';
if (file_exists($canonical)) {
    include $canonical;
    return;
}

// Fallback: brief informative HTML if canonical file is missing
http_response_code(500);
?><div style="font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial;color:#333;padding:18px;">
  <strong>Header missing</strong><div>Expected <?php echo htmlspecialchars($canonical); ?> — contact the administrator.</div>
</div>

