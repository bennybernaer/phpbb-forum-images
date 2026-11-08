# Changelog

All notable changes to **Forum Images** are documented here.

## 1.0.2 — 2026-11-08

### Changed
- Evolution carousel navigation buttons enlarged from 18 × 18 px to 25 × 25 px.
- Evolution navigation icons use `var(--ps-font-normal)` instead of `var(--ps-font-tiny)`.
- Evolution navigation hover and focus icon colour changed to `var(--color01)`.
- Existing Evolution backgrounds, borders and other carousel styling are unchanged; prosilver/default styling is unaffected.

### Notes
- CSS-only visual refinement; no database or ACP changes are needed.

## 1.0.1 — 2026-11-02

### Added
- Font Awesome previous/next controls for the continuous carousel using phpBB 4's `Icon()` helper.
- Matching compact navigation styling for prosilver/default and Evolution.
- Accessible English and Dutch labels for the carousel controls.
- Local thumbnail/cache copies for attachment images to avoid intermittent attachment-route 403 responses.

### Changed
- Manual carousel navigation moves one image at a time with a smooth JavaScript animation while automatic scrolling continues.
- Carousel controls are displayed below the image strip.

### Notes
- No database or ACP setting changes are required for this update.
- Attachment originals are never modified; cached images are stored in the extension's `thumbs/` directory.
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
- Uses phpBB 4's `phpbb_storage_attachment` route as a fallback for attachment URLs.
- Supports phpBB 4 / s9e TextFormatter image markup with a legacy `[img]` fallback.
- Requires PHP 8.2+ and targets phpBB 4.0.x.

### Notes
- No custom permissions are added; existing phpBB forum permissions are respected.
- No forum is selected by default. The target forum must be chosen in the ACP after installation.
