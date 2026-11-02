# Forum Images

phpBB 4.0 extension by **bennybernaer**.

Displays recent images from one selected forum above the forum title. Supports phpBB 4 TextFormatter `[img]` images and image attachments, an optional continuous carousel, configurable image count, minimum count, thumbnail size, and excluded topic IDs.

## Features

- Select one target forum from the ACP
- Use `[img]` images, image attachments, or both
- Continuous carousel or static row
- Compact Font Awesome previous/next controls using phpBB 4's `Icon()` helper
- Smooth manual navigation one image at a time while automatic scrolling continues
- Local cached thumbnails for attachment images
- Configurable maximum/minimum image count
- Configurable thumbnail size
- Exclude specific topic IDs
- Respects existing phpBB forum permissions and content visibility
- Separate prosilver/default and Evolution styling
- English and Dutch language files

## Requirements

- phpBB 4.0.x
- PHP 8.2 or newer

## Installation

1. Download `downloads/forumimages-1.0.1.zip` or the latest GitHub Release.
2. Extract it so the extension is located at `ext/bennybernaer/forumimages/`.
3. Go to **ACP > Customise > Manage extensions**.
4. Enable **Forum Images**.
5. Open the extension settings and select the target forum.

Attachment thumbnails are generated automatically in `ext/bennybernaer/forumimages/thumbs/`. The original attachment files are never modified. If a cached copy cannot be created, the extension falls back to phpBB's normal attachment route.

## Default settings

No forum selected, both image sources, carousel enabled, 9 images, minimum 1 image, 120 px thumbnails.

## Permissions

The extension adds no custom permissions. It respects the existing phpBB forum permissions and only displays images when the current user can list and read the selected forum. Only approved posts/topics are used, and private-message attachments are excluded.

See `CHANGELOG.md` for release details.

## License

GPL-2.0-only
