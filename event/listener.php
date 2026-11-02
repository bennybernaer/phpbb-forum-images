<?php
/**
 * Forum Images. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 bennybernaer
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace bennybernaer\forumimages\event;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper as controller_helper;
use phpbb\db\driver\driver_interface;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    public function __construct(
        protected config $config,
        protected driver_interface $db,
        protected template $template,
        protected auth $auth,
        protected controller_helper $controller_helper,
        protected user $user,
        protected string $phpbb_root_path,
        protected string $php_ext
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.viewforum_get_topic_data' => 'load_images',
        ];
    }

    public function load_images($event): void
    {
        $current_forum_id = (int) $event['forum_id'];
        $target_forum_id = (int) ($this->config['forumimages_forum_id'] ?? 0);

        if ($target_forum_id <= 0 || $current_forum_id !== $target_forum_id)
        {
            return;
        }

        if (!$this->auth->acl_gets('f_list', 'f_list_topics', 'f_read', $target_forum_id))
        {
            return;
        }

        $limit = max(1, min(40, (int) ($this->config['forumimages_count'] ?? 9)));
        $minimum = max(1, min($limit, (int) ($this->config['forumimages_min_count'] ?? 1)));
        $size = max(30, min(200, (int) ($this->config['forumimages_size'] ?? 120)));
        $source = max(0, min(2, (int) ($this->config['forumimages_source'] ?? 2)));
        $carousel = (bool) ($this->config['forumimages_carousel'] ?? 1);
        $excluded_topics = $this->parse_topic_ids((string) ($this->config['forumimages_exclude_topics'] ?? ''));
        $candidates = [];

        if ($source === 1 || $source === 2)
        {
            $this->collect_attachment_images($target_forum_id, $limit * 4, $candidates, $excluded_topics, $size);
        }

        if ($source === 0 || $source === 2)
        {
            $this->collect_post_images($target_forum_id, $limit * 4, $candidates, $excluded_topics);
        }

        if (!$candidates)
        {
            return;
        }

        usort($candidates, static fn (array $a, array $b): int => $b['time'] <=> $a['time']);

        $seen = [];
        $images = [];

        foreach ($candidates as $candidate)
        {
            $key = $candidate['image'];

            if (isset($seen[$key]))
            {
                continue;
            }

            $seen[$key] = true;
            $images[] = $candidate;

            if (count($images) >= $limit)
            {
                break;
            }
        }

        if (count($images) < $minimum)
        {
            return;
        }

        $this->user->add_lang_ext('bennybernaer/forumimages', 'common');

        foreach ($images as $image)
        {
            $this->template->assign_block_vars('forumimages', [
                'IMAGE_URL' => $image['image'],
                'POST_URL'  => $image['post'],
                'ALT'       => $image['alt'],
            ]);
        }

        $this->template->assign_vars([
            'FORUMIMAGES_DISPLAY'  => true,
            'FORUMIMAGES_SIZE'     => $size,
            'FORUMIMAGES_CAROUSEL' => $carousel,
        ]);
    }

    protected function collect_attachment_images(int $forum_id, int $limit, array &$candidates, array $excluded_topics, int $size): void
    {
        $sql = 'SELECT a.attach_id, a.real_filename, a.physical_filename, a.mimetype, a.filetime, a.post_msg_id, p.post_subject
                FROM ' . ATTACHMENTS_TABLE . ' a
                INNER JOIN ' . POSTS_TABLE . ' p ON p.post_id = a.post_msg_id
                INNER JOIN ' . TOPICS_TABLE . ' t ON t.topic_id = p.topic_id
                WHERE p.forum_id = ' . $forum_id . '
                    AND p.post_visibility = ' . ITEM_APPROVED . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                    ' . ($excluded_topics ? 'AND ' . $this->db->sql_in_set('p.topic_id', $excluded_topics, true) : '') . '
                    AND a.in_message = 0
                    AND a.is_orphan = 0
                    AND a.mimetype ' . $this->db->sql_like_expression('image/' . $this->db->get_any_char()) . '
                ORDER BY a.filetime DESC';

        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $filename = (string) $row['real_filename'];
            $subject = (string) $row['post_subject'];

            $image_url = $this->get_cached_attachment_url($row, $size);

            if ($image_url === '')
            {
                $image_url = $this->controller_helper->route('phpbb_storage_attachment', [
                    'id' => (int) $row['attach_id'],
                    'filename' => $filename,
                ]);
            }

            $candidates[] = [
                'image' => $image_url,
                'post' => append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'p=' . (int) $row['post_msg_id']) . '#p' . (int) $row['post_msg_id'],
                'alt' => $this->escape($filename !== '' ? $filename : $subject),
                'time' => (int) $row['filetime'],
            ];
        }

        $this->db->sql_freeresult($result);
    }

    /**
     * Build a browser-readable cache image for an attachment.
     *
     * This avoids repeatedly serving carousel images through phpBB's attachment
     * controller. The original attachment is never modified.
     */
    protected function get_cached_attachment_url(array $row, int $display_size): string
    {
        $attach_id = (int) ($row['attach_id'] ?? 0);
        $filetime = (int) ($row['filetime'] ?? 0);
        $physical_filename = basename((string) ($row['physical_filename'] ?? ''));
        $mimetype = strtolower((string) ($row['mimetype'] ?? ''));

        if ($attach_id <= 0 || $physical_filename === '' || !str_starts_with($mimetype, 'image/'))
        {
            return '';
        }

        $extension = $this->cache_extension_for_mimetype($mimetype);

        if ($extension === '')
        {
            return '';
        }

        $storage_path = trim((string) ($this->config['storage\\attachment\\config\\path'] ?? 'files'), '/\\');
        $source_path = $this->phpbb_root_path . ($storage_path !== '' ? $storage_path . '/' : '') . $physical_filename;

        if (!is_file($source_path) || !is_readable($source_path))
        {
            return '';
        }

        // Validate that the source really is a raster image before exposing a static copy.
        $image_info = @getimagesize($source_path);

        if ($image_info === false || empty($image_info[0]) || empty($image_info[1]))
        {
            return '';
        }

        $cache_dir = $this->phpbb_root_path . 'ext/bennybernaer/forumimages/thumbs';

        if (!is_dir($cache_dir) && !@mkdir($cache_dir, 0755, true) && !is_dir($cache_dir))
        {
            return '';
        }

        if (!is_writable($cache_dir))
        {
            return '';
        }

        $target_size = max(60, min(400, $display_size * 2));
        $cache_name = sprintf('attachment_%d_%d_%d.%s', $attach_id, $filetime, $target_size, $extension);
        $cache_path = $cache_dir . '/' . $cache_name;

        if (!is_file($cache_path) || (int) @filesize($cache_path) <= 0)
        {
            foreach (glob($cache_dir . '/attachment_' . $attach_id . '_*') ?: [] as $old_cache)
            {
                if ($old_cache !== $cache_path && is_file($old_cache))
                {
                    @unlink($old_cache);
                }
            }

            if (!$this->create_cached_image($source_path, $cache_path, $mimetype, (int) $image_info[0], (int) $image_info[1], $target_size))
            {
                return '';
            }
        }

        return $this->phpbb_root_path . 'ext/bennybernaer/forumimages/thumbs/' . rawurlencode($cache_name);
    }

    protected function create_cached_image(string $source_path, string $cache_path, string $mimetype, int $width, int $height, int $target_size): bool
    {
        $temporary_path = $cache_path . '.tmp-' . bin2hex(random_bytes(4));

        // GIF/AVIF and small images are copied as-is. This preserves animation and
        // avoids unnecessary recompression while still bypassing the attachment route.
        if (in_array($mimetype, ['image/gif', 'image/avif'], true) || max($width, $height) <= $target_size)
        {
            $copied = @copy($source_path, $temporary_path);

            if ($copied)
            {
                return $this->finish_cache_file($temporary_path, $cache_path);
            }

            return false;
        }

        if (!function_exists('imagecreatefromstring') || !function_exists('imagecreatetruecolor'))
        {
            $copied = @copy($source_path, $temporary_path);

            if ($copied)
            {
                return $this->finish_cache_file($temporary_path, $cache_path);
            }

            return false;
        }

        // Avoid excessive memory use on exceptionally large uploads.
        if (($width * $height) > 20000000)
        {
            $copied = @copy($source_path, $temporary_path);

            if ($copied)
            {
                return $this->finish_cache_file($temporary_path, $cache_path);
            }

            return false;
        }

        $contents = @file_get_contents($source_path);

        if ($contents === false)
        {
            return false;
        }

        $source = @imagecreatefromstring($contents);
        unset($contents);

        if ($source === false)
        {
            return false;
        }

        $scale = min($target_size / $width, $target_size / $height, 1);
        $new_width = max(1, (int) round($width * $scale));
        $new_height = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($new_width, $new_height);

        if ($target === false)
        {
            imagedestroy($source);
            return false;
        }

        if (in_array($mimetype, ['image/png', 'image/webp'], true))
        {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $new_width, $new_height, $transparent);
        }

        $resampled = imagecopyresampled($target, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($source);

        if (!$resampled)
        {
            imagedestroy($target);
            return false;
        }

        $written = match ($mimetype) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => function_exists('imagejpeg') && @imagejpeg($target, $temporary_path, 88),
            'image/png' => function_exists('imagepng') && @imagepng($target, $temporary_path, 6),
            'image/webp' => function_exists('imagewebp') && @imagewebp($target, $temporary_path, 85),
            default => false,
        };

        imagedestroy($target);

        if (!$written)
        {
            @unlink($temporary_path);
            $copied = @copy($source_path, $temporary_path);

            if (!$copied)
            {
                return false;
            }
        }

        return $this->finish_cache_file($temporary_path, $cache_path);
    }

    protected function finish_cache_file(string $temporary_path, string $cache_path): bool
    {
        if (!is_file($temporary_path) || (int) @filesize($temporary_path) <= 0)
        {
            @unlink($temporary_path);
            return false;
        }

        if (!@rename($temporary_path, $cache_path))
        {
            @unlink($cache_path);

            if (!@copy($temporary_path, $cache_path))
            {
                @unlink($temporary_path);
                return false;
            }

            @unlink($temporary_path);
        }

        @chmod($cache_path, 0644);
        return is_file($cache_path) && (int) @filesize($cache_path) > 0;
    }

    protected function cache_extension_for_mimetype(string $mimetype): string
    {
        return match ($mimetype) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            'image/bmp', 'image/x-ms-bmp' => 'bmp',
            default => '',
        };
    }

    protected function collect_post_images(int $forum_id, int $limit, array &$candidates, array $excluded_topics): void
    {
        $img_markup = $this->db->sql_like_expression($this->db->get_any_char() . '<IMG' . $this->db->get_any_char());
        $legacy_img = $this->db->sql_like_expression($this->db->get_any_char() . '[img' . $this->db->get_any_char());

        $sql = 'SELECT p.post_id, p.post_subject, p.post_text, p.post_time
                FROM ' . POSTS_TABLE . ' p
                INNER JOIN ' . TOPICS_TABLE . ' t ON t.topic_id = p.topic_id
                WHERE p.forum_id = ' . $forum_id . '
                    AND p.post_visibility = ' . ITEM_APPROVED . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                    ' . ($excluded_topics ? 'AND ' . $this->db->sql_in_set('p.topic_id', $excluded_topics, true) : '') . '
                    AND (p.post_text ' . $img_markup . ' OR p.post_text ' . $legacy_img . ')
                ORDER BY p.post_time DESC';

        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $post_text = (string) $row['post_text'];
            $urls = $this->extract_image_urls($post_text);

            foreach ($urls as $url)
            {
                $candidates[] = [
                    'image' => $this->escape($url),
                    'post' => append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'p=' . (int) $row['post_id']) . '#p' . (int) $row['post_id'],
                    'alt' => $this->escape((string) $row['post_subject']),
                    'time' => (int) $row['post_time'],
                ];
            }
        }

        $this->db->sql_freeresult($result);
    }

    protected function extract_image_urls(string $post_text): array
    {
        $urls = [];

        // phpBB 4 / s9e TextFormatter stores [img] as <IMG src="..."> markup.
        if (preg_match_all('#<IMG\\b[^>]*\\bsrc="([^"]+)"[^>]*>#i', $post_text, $matches))
        {
            foreach ($matches[1] as $raw_url)
            {
                $url = html_entity_decode((string) $raw_url, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if ($this->is_http_url($url))
                {
                    $urls[] = $url;
                }
            }
        }

        // Fallback for legacy/unreparsed posts that may still contain literal BBCode.
        if (preg_match_all('#\\[img(?::[a-z0-9]+)?\\](.*?)\\[/img(?::[a-z0-9]+)?\\]#is', $post_text, $legacy_matches))
        {
            foreach ($legacy_matches[1] as $raw_url)
            {
                $url = html_entity_decode(trim((string) $raw_url), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if ($this->is_http_url($url))
                {
                    $urls[] = $url;
                }
            }
        }

        return array_values(array_unique($urls));
    }

    protected function is_http_url(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return $scheme === 'http' || $scheme === 'https';
    }

    protected function parse_topic_ids(string $value): array
    {
        if ($value === '')
        {
            return [];
        }

        $ids = preg_split('/[\\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn (int $id): bool => $id > 0);

        return array_values(array_unique($ids));
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
