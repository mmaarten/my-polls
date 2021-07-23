<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Item;

class Result
{
    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'addMetaBoxes']);
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-result-meta-box', __('Result', 'my-polls'), [__CLASS__, 'render'], 'poll', 'side');
    }

    public static function render($post = null)
    {
        $poll = new Poll($post);

        $items = $poll->getItems();

        if (! $items) {
            Helpers::adminNotice(__('No items found.', 'my-polls'), 'info', true);
            return;
        }

        echo '<ol>';

        $result = [];

        foreach ($items as $item) {
            $result[$item->ID] = count($poll->getVotesByItem($item->ID));
        }

        asort($result, SORT_NUMERIC);

        $result = array_reverse($result, true);

        foreach ($result as $item_id => $votes) {
            $item = new Item($item_id);
            printf(
                '<li><strong>%1$s</strong> (%2$d %3$s)</li>',
                esc_html($item->getContent()),
                $votes,
                esc_html(_n('vote', 'votes', $votes, 'my-polls'))
            );
        }

        echo '</ol>';
    }
}
