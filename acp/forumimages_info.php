<?php
/**
 * Forum Images. An extension for the phpBB Forum Software package.
 */

namespace bennybernaer\forumimages\acp;

class forumimages_info
{
    public function module(): array
    {
        return [
            'filename' => '\\bennybernaer\\forumimages\\acp\\forumimages_module',
            'title'    => 'ACP_FORUMIMAGES_TITLE',
            'modes'    => [
                'settings' => [
                    'title' => 'ACP_FORUMIMAGES_SETTINGS',
                    'auth'  => 'ext_bennybernaer/forumimages && acl_a_board',
                    'cat'   => ['ACP_FORUMIMAGES_TITLE'],
                ],
            ],
        ];
    }
}
