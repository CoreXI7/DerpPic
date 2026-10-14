<?php
// Shared helpers for the gallery. The main job here is making sure a path
// from the URL can never escape the media folder (the old get_image.php
// would read any file the web server could see).

$CONFIG = require __DIR__ . '/../config.php';

/** Absolute path of the media root, or a 500 if it's missing. */
function media_root(): string {
    global $CONFIG;
    $root = realpath($CONFIG['media_root']);
    if ($root === false || !is_dir($root)) {
        http_response_code(500);
        exit('Media folder not found. Check MEDIA_ROOT / your volume mount.');
    }
    return $root;
}

/**
 * Turn a user-supplied relative path into an absolute path inside the media
 * root. Returns null if it doesn't exist or points outside the root.
 */
function safe_path(string $rel): ?string {
    $root = media_root();
    $rel  = str_replace('\\', '/', $rel);
    if (str_contains($rel, "\0")) return null;
    $full = realpath($root . '/' . ltrim($rel, '/'));
    if ($full === false) return null;
    if ($full !== $root && !str_starts_with($full, $root . '/')) return null;
    // Hide dotfiles / dot-folders anywhere in the path.
    foreach (explode('/', substr($full, strlen($root))) as $part) {
        if ($part !== '' && $part[0] === '.') return null;
    }
    return $full;
}

/** Path relative to the media root, for building URLs. */
function rel_path(string $full): string {
    return ltrim(substr($full, strlen(media_root())), '/');
}

function is_image(string $file): bool {
    global $CONFIG;
    return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $CONFIG['extensions'], true);
}

/** Folders and images directly inside $dir, both sorted naturally. */
function list_dir(string $dir): array {
    $folders = $images = [];
    foreach (scandir($dir) ?: [] as $name) {
        if ($name[0] === '.') continue;
        $path = "$dir/$name";
        if (is_dir($path))                        $folders[] = $name;
        elseif (is_file($path) && is_image($name)) $images[]  = $name;
    }
    natcasesort($folders);
    natcasesort($images);
    return [array_values($folders), array_values($images)];
}

/** First image found in a folder (searching one level of subfolders), for album covers. */
function folder_cover(string $dir): ?string {
    [$folders, $images] = list_dir($dir);
    if ($images) return "$dir/{$images[0]}";
    foreach ($folders as $f) {
        [, $sub] = list_dir("$dir/$f");
        if ($sub) return "$dir/$f/{$sub[0]}";
    }
    return null;
}

/** URL-encode each path segment but keep the slashes. */
function url_path(string $rel): string {
    return implode('/', array_map('rawurlencode', explode('/', $rel)));
}

function h(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
