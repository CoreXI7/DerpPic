<?php
// Twitch stream + chat. Replaces the old Flash embed, which no browser runs anymore.
// Twitch requires a "parent" parameter matching the site's domain, so it's
// filled in from whatever domain the page is opened on.
require __DIR__ . '/layout.php';
$cfg     = require __DIR__ . '/config.php';
$channel = preg_replace('/[^A-Za-z0-9_]/', '', $cfg['twitch_channel']);
page_header('Stream', 'Stream');
?>
  <h1><?= htmlspecialchars($cfg['site_title'], ENT_QUOTES, 'UTF-8') ?>'s stream</h1>

  <div class="stream" id="stream" data-channel="<?= $channel ?>">
    <iframe class="player" title="Twitch stream" allowfullscreen></iframe>
    <iframe class="chat" title="Twitch chat"></iframe>
  </div>
  <p class="crumbs">Not loading? Watch it on <a href="https://www.twitch.tv/<?= $channel ?>" target="_blank" rel="noopener">twitch.tv/<?= $channel ?></a>.</p>

<script>
(() => {
  const box = document.getElementById('stream');
  const ch = encodeURIComponent(box.dataset.channel);
  const parent = encodeURIComponent(location.hostname);
  box.querySelector('.player').src =
    `https://player.twitch.tv/?channel=${ch}&parent=${parent}&muted=true`;
  box.querySelector('.chat').src =
    `https://www.twitch.tv/embed/${ch}/chat?parent=${parent}`;
})();
</script>
<?php page_footer();
