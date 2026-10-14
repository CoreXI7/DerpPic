<?php
require __DIR__ . '/layout.php';
$cfg = require __DIR__ . '/config.php';
page_header('Home', 'Home');
?>
  <section class="hero">
    <h1><?= htmlspecialchars($cfg['site_title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <nav class="top" style="justify-content:center">
      <a class="celestia" href="/gallery/">Gallery</a>
      <a class="celestia" href="/stream.php">Stream</a>
    </nav>
    <a href="/gallery/"><img class="nom" src="/assets/nom.png" alt="Open the gallery"></a>
  </section>
<?php page_footer();
