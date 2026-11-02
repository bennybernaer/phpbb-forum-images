<?php
/**
 * Forum Images. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 bennybernaer
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace bennybernaer\forumimages\migrations;

use phpbb\db\migration\migration;

class v_1_0_0 extends migration
{
    public function effectively_installed(): bool
    {
        return isset($this->config['forumimages_version'])
            && version_compare((string) $this->config['forumimages_version'], '1.0.0', '>=');
    }

    public static function depends_on(): array
    {
        return [
            '\\phpbb\\db\\migration\\data\\v400\\dev',
        ];
    }

    public function update_data(): array
    {
        return [
            ['config.add', ['forumimages_forum_id', 0]],
            ['config.add', ['forumimages_source', 2]],
            ['config.add', ['forumimages_carousel', 1]],
            ['config.add', ['forumimages_count', 9]],
            ['config.add', ['forumimages_min_count', 1]],
            ['config.add', ['forumimages_size', 120]],
            ['config.add', ['forumimages_exclude_topics', '']],
            ['config.add', ['forumimages_version', '1.0.0']],
            ['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'ACP_FORUMIMAGES_TITLE']],
            ['module.add', [
                'acp',
                'ACP_FORUMIMAGES_TITLE',
                [
                    'module_basename' => '\\bennybernaer\\forumimages\\acp\\forumimages_module',
                    'module_langname' => 'ACP_FORUMIMAGES_SETTINGS',
                    'module_mode' => 'settings',
                    'module_auth' => 'ext_bennybernaer/forumimages && acl_a_board',
                ],
            ]],
        ];
    }
}
