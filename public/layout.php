<?php
// Shared page header/footer so every page has the same look and nav.

function page_header(string $title, string $active = ''): void {
    $cfg  = require __DIR__ . '/config.php';
    $site = htmlspecialchars($cfg['site_title'], ENT_QUOTES, 'UTF-8');
    $t    = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $nav  = ['/' => 'Home', '/gallery/' => 'Gallery', '/stream.php' => 'Stream'];
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $t ?> · <?= $site ?></title>
  <link rel="icon" href="/assets/favicon.ico">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="wrap">
  <header class="top">
    <a href="/"><img class="logo" src="/assets/logo.png" alt="<?= $site ?>"></a>
    <nav>
      <?php foreach ($nav as $href => $label): ?>
        <a href="<?= $href ?>"<?= $label === $active ? ' aria-current="page"' : '' ?>><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
  </header>
    <?php
}

function page_footer(): void {
    echo "</div>\n</body>\n</html>\n";
}
