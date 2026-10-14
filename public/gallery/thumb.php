<?php
// Thumbnail generator (replaces the old get_image.php).
// - Only reads images inside the media folder.
// - Size is fixed by config, not taken from the URL.
// - Thumbnails are cached on disk, so each one is only made once.
// - Keeps transparency (outputs WebP).

require __DIR__ . '/lib.php';

$full = safe_path($_GET['p'] ?? '');
if ($full === null || !is_file($full) || !is_image($full)) {
    http_response_code(404);
    exit;
}

$size  = (int)$CONFIG['thumb_size'];
$cache = rtrim($CONFIG['thumb_cache'], '/');
$key   = sha1($full . '|' . filemtime($full) . '|' . filesize($full) . '|' . $size);
$out   = "$cache/" . substr($key, 0, 2) . "/$key.webp";

function send_thumb(string $file): never {
    header('Content-Type: image/webp');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=2592000, immutable');
    readfile($file);
    exit;
}

if (is_file($out)) send_thumb($out);

// Refuse absurdly large images so one file can't eat all the RAM.
$info = @getimagesize($full);
if ($info === false || $info[0] * $info[1] > 60_000_000) {
    http_response_code(415);
    exit;
}
[$w, $h] = $info;

$src = match ($info[2]) {
    IMAGETYPE_JPEG => @imagecreatefromjpeg($full),
    IMAGETYPE_PNG  => @imagecreatefrompng($full),
    IMAGETYPE_GIF  => @imagecreatefromgif($full),   // first frame
    IMAGETYPE_WEBP => @imagecreatefromwebp($full),
    default        => false,
};
if (!$src) { http_response_code(415); exit; }

// Fit inside a $size x $size box, never upscale.
$scale = min(1, $size / max($w, $h));
$tw = max(1, (int)round($w * $scale));
$th = max(1, (int)round($h * $scale));

$dst = imagecreatetruecolor($tw, $th);
imagealphablending($dst, false);
imagesavealpha($dst, true);
imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
imagedestroy($src);

@mkdir(dirname($out), 0775, true);
$tmp = "$out.tmp" . getmypid();
if (imagewebp($dst, $tmp, 82)) {
    rename($tmp, $out);
    imagedestroy($dst);
    send_thumb($out);
}

// Cache not writable: still serve the thumbnail, just don't save it.
@unlink($tmp);
header('Content-Type: image/webp');
header('Cache-Control: no-store');
imagewebp($dst, null, 82);
imagedestroy($dst);
