<?php
// DerpPic gallery: browse folders (albums) and images under the media root.
// URL: /gallery/?a=Some/Album&page=2

require __DIR__ . '/lib.php';
require __DIR__ . '/../layout.php';

$albumRel = trim((string)($_GET['a'] ?? ''), '/');
$dir = safe_path($albumRel);
if ($dir === null || !is_dir($dir)) {
    http_response_code(404);
    page_header('Not found', 'Gallery');
    echo '<p class="empty">That album doesn\'t exist. <a href="/gallery/">Back to the gallery</a></p>';
    page_footer();
    exit;
}
$albumRel = rel_path($dir);

[$folders, $images] = list_dir($dir);

// Pagination for big folders.
$perPage = (int)$CONFIG['per_page'];
$pages   = max(1, (int)ceil(count($images) / $perPage));
$page    = min($pages, max(1, (int)($_GET['page'] ?? 1)));
$shown   = array_slice($images, ($page - 1) * $perPage, $perPage);

$title = $albumRel === '' ? 'Gallery' : basename($albumRel);
page_header($title, 'Gallery');

function album_link(string $rel, int $page = 1): string {
    $q = [];
    if ($rel !== '') $q['a'] = $rel;
    if ($page > 1)   $q['page'] = $page;
    return '/gallery/' . ($q ? '?' . http_build_query($q) : '');
}
?>
  <h1><?= h($title) ?></h1>

  <div class="crumbs">
    <a href="/gallery/">Gallery</a>
    <?php
    $acc = '';
    foreach ($albumRel === '' ? [] : explode('/', $albumRel) as $part) {
        $acc = ltrim("$acc/$part", '/');
        echo ' / <a href="' . h(album_link($acc)) . '">' . h($part) . '</a>';
    }
    ?>
  </div>

  <?php if (!$folders && !$images): ?>
    <p class="empty">Nothing here yet. Drop some pictures into this folder and refresh.</p>
  <?php endif; ?>

  <?php if ($folders && $page === 1): ?>
    <div class="grid" style="margin-bottom:24px">
      <?php foreach ($folders as $f):
          $fFull = "$dir/$f";
          $fRel  = ltrim("$albumRel/$f", '/');
          $cover = folder_cover($fFull);
          [$subF, $subI] = list_dir($fFull);
          $bits = [];
          if ($subI) $bits[] = count($subI) . ' pic' . (count($subI) === 1 ? '' : 's');
          if ($subF) $bits[] = count($subF) . ' folder' . (count($subF) === 1 ? '' : 's');
      ?>
        <a class="tile album" href="<?= h(album_link($fRel)) ?>">
          <div class="pic">
            <?php if ($cover): ?>
              <img loading="lazy" alt="" src="thumb.php?p=<?= h(rawurlencode(rel_path($cover))) ?>">
            <?php else: ?>
              <span class="folder-icon" aria-hidden="true">📁</span>
            <?php endif; ?>
          </div>
          <div class="label"><?= h($f) ?></div>
          <div class="count"><?= h($bits ? implode(' · ', $bits) : 'empty') ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($shown): ?>
    <div class="grid" id="photos">
      <?php foreach ($shown as $i => $img):
          $rel = ltrim("$albumRel/$img", '/');
      ?>
        <a class="tile" href="/media/<?= h(url_path($rel)) ?>" data-i="<?= $i ?>">
          <div class="pic">
            <img loading="lazy" alt="<?= h($img) ?>" src="thumb.php?p=<?= h(rawurlencode($rel)) ?>">
          </div>
          <div class="label"><?= h($img) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <nav class="pager">
      <?php if ($page > 1): ?><a href="<?= h(album_link($albumRel, $page - 1)) ?>">‹ Prev</a><?php endif; ?>
      <span>Page <?= $page ?> of <?= $pages ?></span>
      <?php if ($page < $pages): ?><a href="<?= h(album_link($albumRel, $page + 1)) ?>">Next ›</a><?php endif; ?>
    </nav>
  <?php endif; ?>

  <div class="lightbox" id="lb" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button class="close" aria-label="Close">✕</button>
    <button class="prev" aria-label="Previous">‹</button>
    <img alt="">
    <button class="next" aria-label="Next">›</button>
    <div class="cap"></div>
  </div>

<script>
(() => {
  const links = [...document.querySelectorAll('#photos a')];
  const lb = document.getElementById('lb');
  if (!links.length || !lb) return;
  const img = lb.querySelector('img'), cap = lb.querySelector('.cap');
  let cur = 0;

  const show = i => {
    cur = (i + links.length) % links.length;
    const a = links[cur];
    img.src = a.href;
    img.alt = a.querySelector('img').alt;
    cap.innerHTML = '';
    const open = document.createElement('a');
    open.href = a.href; open.target = '_blank'; open.rel = 'noopener';
    open.textContent = img.alt;
    cap.append(open, ` (${cur + 1} / ${links.length})`);
    lb.classList.add('open');
  };
  const close = () => { lb.classList.remove('open'); img.removeAttribute('src'); };

  links.forEach((a, i) => a.addEventListener('click', e => {
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
    e.preventDefault(); show(i);
  }));
  lb.querySelector('.close').onclick = close;
  lb.querySelector('.prev').onclick = () => show(cur - 1);
  lb.querySelector('.next').onclick = () => show(cur + 1);
  lb.addEventListener('click', e => { if (e.target === lb) close(); });
  document.addEventListener('keydown', e => {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') show(cur - 1);
    if (e.key === 'ArrowRight') show(cur + 1);
  });

  // Swipe on phones.
  let x0 = null;
  lb.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
  lb.addEventListener('touchend', e => {
    if (x0 === null) return;
    const dx = e.changedTouches[0].clientX - x0;
    if (Math.abs(dx) > 50) show(cur + (dx < 0 ? 1 : -1));
    x0 = null;
  });
})();
</script>
<?php page_footer();
