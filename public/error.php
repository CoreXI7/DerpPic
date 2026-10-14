<?php
// Friendly error page (Apache ErrorDocument points here).
require __DIR__ . '/layout.php';
$code = (int)($_SERVER['REDIRECT_STATUS'] ?? http_response_code() ?: 404);
if ($code < 400) $code = 404;
http_response_code($code);
$msg = [
    403 => 'You\'re not allowed in there.',
    404 => 'Whoops, that page wandered off.',
    500 => 'Something broke on the server side.',
][$code] ?? 'Something went wrong.';
page_header("Error $code");
?>
  <section class="hero">
    <h1>Error <?= $code ?></h1>
    <p><?= $msg ?></p>
    <img src="/assets/Error.gif" alt="" style="max-width:min(582px,90vw);height:auto">
    <p><a href="/">Take me home</a></p>
  </section>
<?php page_footer();
