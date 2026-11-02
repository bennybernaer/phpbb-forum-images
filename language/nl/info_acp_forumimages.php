<?php
/**
 * Forum Images taalbestand (Nederlands).
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_FORUMIMAGES_TITLE' => 'Forumafbeeldingen',
    'ACP_FORUMIMAGES_SETTINGS' => 'Instellingen',
    'ACP_FORUMIMAGES_EXPLAIN' => 'Toont recente afbeeldingen boven de forumtitel van één gekozen forum. De instellingen hieronder zijn gebaseerd op de bruikbare opties uit de oorspronkelijke Images from posts-extensie.',
    'ACP_FORUMIMAGES_DISPLAY_SETTINGS' => 'Weergave',
    'ACP_FORUMIMAGES_IMAGE_SETTINGS' => 'Afbeeldingen',
    'ACP_FORUMIMAGES_FORUM' => 'Forum',
    'ACP_FORUMIMAGES_FORUM_NONE' => '— Kies een forum —',
    'ACP_FORUMIMAGES_FORUM_EXPLAIN' => 'Kies het forum waarin het afbeeldingsblok wordt getoond.',
    'ACP_FORUMIMAGES_SOURCE' => 'Bron van afbeeldingen',
    'ACP_FORUMIMAGES_SOURCE_EXPLAIN' => 'Bepaal of afbeeldingen uit [img]-tags, afbeeldingsbijlagen of beide worden opgehaald.',
    'ACP_FORUMIMAGES_SOURCE_IMG' => 'Alleen [img]-afbeeldingen',
    'ACP_FORUMIMAGES_SOURCE_ATTACHMENTS' => 'Alleen afbeeldingsbijlagen',
    'ACP_FORUMIMAGES_SOURCE_BOTH' => '[img]-afbeeldingen en bijlagen',
    'ACP_FORUMIMAGES_CAROUSEL' => 'Carousel gebruiken',
    'ACP_FORUMIMAGES_CAROUSEL_EXPLAIN' => 'Laat de afbeeldingen automatisch en oneindig doorlopen. Indien uitgeschakeld worden de afbeeldingen als vaste rij weergegeven.',
    'ACP_FORUMIMAGES_COUNT' => 'Aantal afbeeldingen',
    'ACP_FORUMIMAGES_COUNT_EXPLAIN' => 'Maximum aantal recente afbeeldingen dat wordt opgehaald. Standaard: 9.',
    'ACP_FORUMIMAGES_MIN_COUNT' => 'Minimum aantal afbeeldingen',
    'ACP_FORUMIMAGES_MIN_COUNT_EXPLAIN' => 'Toon het blok alleen wanneer minstens dit aantal afbeeldingen beschikbaar is.',
    'ACP_FORUMIMAGES_SIZE' => 'Afbeeldingsgrootte',
    'ACP_FORUMIMAGES_SIZE_EXPLAIN' => 'Breedte en hoogte van de thumbnails in pixels. Standaard: 120.',
    'ACP_FORUMIMAGES_EXCLUDE_TOPICS' => 'Onderwerpen uitsluiten',
    'ACP_FORUMIMAGES_EXCLUDE_TOPICS_EXPLAIN' => 'Optioneel: topic-ID’s die niet mogen worden gebruikt, gescheiden door komma’s. Bijvoorbeeld: 12, 45, 81.',
]);
