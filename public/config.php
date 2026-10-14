<?php
// DerpPic settings. Most of these can be overridden with environment
// variables in docker-compose.yaml, so you rarely need to edit this file.

return [
    'site_title'     => getenv('SITE_TITLE')     ?: 'Victor Suarez',
    'twitch_channel' => getenv('TWITCH_CHANNEL') ?: 'corexi7',

    // Where your pictures live (mounted read-only into the container).
    'media_root'     => getenv('MEDIA_ROOT')     ?: '/srv/media',
    // Where generated thumbnails are cached (must be writable).
    'thumb_cache'    => getenv('THUMB_CACHE')    ?: '/srv/cache',

    'thumb_size'     => 300,   // longest edge, in pixels
    'per_page'       => 60,    // images per gallery page

    // File types the gallery will show.
    'extensions'     => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
];
