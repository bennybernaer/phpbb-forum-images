<?php
/**
 * Forum Images. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 bennybernaer
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace bennybernaer\forumimages\acp;

class forumimages_module
{
    public string $u_action = '';
    public string $tpl_name = '';
    public string $page_title = '';

    public function main($id, $mode): void
    {
        global $config, $request, $template, $user;

        $user->add_lang_ext('bennybernaer/forumimages', 'info_acp_forumimages');

        $this->tpl_name = 'acp_forumimages';
        $this->page_title = 'ACP_FORUMIMAGES_TITLE';

        add_form_key('acp_forumimages');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('acp_forumimages'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            $forum_id = max(0, $request->variable('forumimages_forum_id', 0));
            $source = max(0, min(2, $request->variable('forumimages_source', 2)));
            $carousel = $request->variable('forumimages_carousel', 1) ? 1 : 0;
            $count = max(1, min(40, $request->variable('forumimages_count', 9)));
            $minimum = max(1, min($count, $request->variable('forumimages_min_count', 1)));
            $size = max(30, min(200, $request->variable('forumimages_size', 120)));
            $exclude_topics = $this->normalise_topic_ids($request->variable('forumimages_exclude_topics', ''));

            $config->set('forumimages_forum_id', $forum_id);
            $config->set('forumimages_source', $source);
            $config->set('forumimages_carousel', $carousel);
            $config->set('forumimages_count', $count);
            $config->set('forumimages_min_count', $minimum);
            $config->set('forumimages_size', $size);
            $config->set('forumimages_exclude_topics', $exclude_topics);

            trigger_error($user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
        }

        $selected_forum_id = (int) ($config['forumimages_forum_id'] ?? 0);
        $forum_options = '<option value="0"' . ($selected_forum_id === 0 ? ' selected' : '') . '>'
            . $user->lang('ACP_FORUMIMAGES_FORUM_NONE') . '</option>'
            . make_forum_select($selected_forum_id, false, false, true, true, false, false);

        $template->assign_vars([
            'U_ACTION' => $this->u_action,
            'S_FORUM_OPTIONS' => $forum_options,
            'FORUMIMAGES_SOURCE' => (int) ($config['forumimages_source'] ?? 2),
            'FORUMIMAGES_CAROUSEL' => (bool) ($config['forumimages_carousel'] ?? 1),
            'FORUMIMAGES_COUNT' => (int) ($config['forumimages_count'] ?? 9),
            'FORUMIMAGES_MIN_COUNT' => (int) ($config['forumimages_min_count'] ?? 1),
            'FORUMIMAGES_SIZE' => (int) ($config['forumimages_size'] ?? 120),
            'FORUMIMAGES_EXCLUDE_TOPICS' => (string) ($config['forumimages_exclude_topics'] ?? ''),
        ]);
    }

    protected function normalise_topic_ids(string $value): string
    {
        $ids = preg_split('/[\\s,;]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn (int $id): bool => $id > 0);

        return implode(', ', array_values(array_unique($ids)));
    }
}
