# Changelog

All notable changes to **Forum Images** are documented here.

## 1.0.1 — 2026-11-02

### Added
- Font Awesome previous/next navigation buttons for the carousel.
- Manual navigation moves one image at a time while automatic scrolling continues.
- Matching navigation styling for prosilver/default and Evolution.
- English and Dutch accessibility labels for the navigation buttons.

### Notes
- No database or ACP setting changes are required for this update.
- The carousel remains continuous and automatic, with no CSS transitions added.

## 1.0.0 — 2026-11-02

### Added
- Displays recent images above the title of one ACP-selected forum.
- Supports phpBB 4 TextFormatter `[img]` images and image attachments.
- Optional continuous, seamless carousel without a visible scrollbar.
- ACP options for forum, image source, carousel, maximum/minimum image count, thumbnail size, and excluded topic IDs.
- Separate styling for prosilver/default (`styles/all`) and Evolution (`styles/Evolution`).
- English and Dutch language files.

### Security and compatibility
- Respects phpBB forum read/list permissions before collecting or displaying images.
- Only uses approved posts and approved topics.
- Excludes private-message and orphaned attachments.
- Uses phpBB 4's `phpbb_storage_attachment` route for attachment URLs.
- Supports phpBB 4 / s9e TextFormatter image markup with a legacy `[img]` fallback.
- Requires PHP 8.2+ and targets phpBB 4.0.x.

### Notes
- No custom permissions are added; existing phpBB forum permissions are respected.
- No forum is selected by default. The target forum must be chosen in the ACP after installation.
