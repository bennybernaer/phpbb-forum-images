<?php
/**
 * Forum Images language file (English).
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_FORUMIMAGES_TITLE' => 'Forum Images',
    'ACP_FORUMIMAGES_SETTINGS' => 'Settings',
    'ACP_FORUMIMAGES_EXPLAIN' => 'Displays recent images above the forum title of one selected forum. The settings below are based on the useful options from the original Images from posts extension.',
    'ACP_FORUMIMAGES_DISPLAY_SETTINGS' => 'Display',
    'ACP_FORUMIMAGES_IMAGE_SETTINGS' => 'Images',
    'ACP_FORUMIMAGES_FORUM' => 'Forum',
    'ACP_FORUMIMAGES_FORUM_NONE' => '— Select a forum —',
    'ACP_FORUMIMAGES_FORUM_EXPLAIN' => 'Select the forum in which the image block is displayed.',
    'ACP_FORUMIMAGES_SOURCE' => 'Image source',
    'ACP_FORUMIMAGES_SOURCE_EXPLAIN' => 'Choose whether to collect images from [img] tags, image attachments, or both.',
    'ACP_FORUMIMAGES_SOURCE_IMG' => '[img] images only',
    'ACP_FORUMIMAGES_SOURCE_ATTACHMENTS' => 'Image attachments only',
    'ACP_FORUMIMAGES_SOURCE_BOTH' => '[img] images and attachments',
    'ACP_FORUMIMAGES_CAROUSEL' => 'Use carousel',
    'ACP_FORUMIMAGES_CAROUSEL_EXPLAIN' => 'Automatically scroll the images in a continuous loop. When disabled, images are displayed as a static row.',
    'ACP_FORUMIMAGES_COUNT' => 'Number of images',
    'ACP_FORUMIMAGES_COUNT_EXPLAIN' => 'Maximum number of recent images to collect. Default: 9.',
    'ACP_FORUMIMAGES_MIN_COUNT' => 'Minimum number of images',
    'ACP_FORUMIMAGES_MIN_COUNT_EXPLAIN' => 'Only display the block when at least this many images are available.',
    'ACP_FORUMIMAGES_SIZE' => 'Image size',
    'ACP_FORUMIMAGES_SIZE_EXPLAIN' => 'Thumbnail width and height in pixels. Default: 120.',
    'ACP_FORUMIMAGES_EXCLUDE_TOPICS' => 'Exclude topics',
    'ACP_FORUMIMAGES_EXCLUDE_TOPICS_EXPLAIN' => 'Optional: topic IDs that should not be used, separated by commas. Example: 12, 45, 81.',
]);
