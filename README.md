# DerpPic (2026 edition)

The 2012 derpcock.com site, rebuilt to run safely in Docker. Same sky-blue look
and Celestia font, now with a real photo gallery and a working Twitch page.

## What's in it

| Page | What it does |
|---|---|
| `/` | Home page |
| `/gallery/` | Browse your pictures. Every subfolder becomes an album. Click a pic for a full-screen viewer (arrow keys / swipe to flip through). |
| `/stream.php` | Your Twitch stream + chat (modern embed; Flash is dead) |

## Run it

```bash
docker compose up -d --build
```

Then open `http://<your-server>:8099`.

## Add pictures

Put images in `./media` (or point the volume in `docker-compose.yaml` at any folder
you already have). Folders become albums, nested folders work, and new pictures
show up on refresh. Supported: jpg, png, gif, webp. Files and folders starting
with a dot are hidden.

## Settings

Set these in `docker-compose.yaml`, then `docker compose up -d`:

- `SITE_TITLE`: name shown on the home page
- `TWITCH_CHANNEL`: Twitch channel for the stream page

Thumbnail size and images-per-page are in `public/config.php`.

## What changed from the 2012 version

- **Security:** the old `get_image.php` would read any file the server could see,
  and `phpinfo.php` exposed the server config. Both are gone. Paths are now locked
  to the media folder, the media folder is read-only and can't run PHP, and only
  image files can be served from it.
- **Thumbnails** are cached (WebP, transparency kept) instead of re-made on every view.
- **Albums** actually work now (the old folder scan never added subfolders).
- **Responsive** layout for phones, plus dark mode.
- **Stream** uses Twitch's current embed instead of Flash.

## Putting it on the internet

Put it behind your reverse proxy (e.g. Nginx Proxy Manager) with HTTPS. For the
Twitch page to work on a domain, it just needs to be opened on that domain; the
page fills in Twitch's required `parent` setting automatically. If the gallery is
private, put Authelia (or other auth) in front of it.
