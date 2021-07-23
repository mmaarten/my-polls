<?php

namespace My\Polls;

use My\Polls\Posts\Poll;

class App
{
    /**
     * Init
     */
    public static function init()
    {
        array_map(function ($class) {
            call_user_func([__NAMESPACE__ . '\\' . $class, 'init']);
        }, [
            'Fields',
            'PostTypes',
            'Form',
            'Result',
            'Notifications',
        ]);

        add_action('init', [__CLASS__, 'loadTextdomain'], 0);
        add_action('acf/save_post', [__CLASS__, 'savePost']);
    }

    /**
     * Load textdomain
     */
    public static function loadTextdomain()
    {
        load_plugin_textdomain('my-polls', false, dirname(plugin_basename(MY_POLLS_PLUGIN_FILE)) . '/languages');
    }

    public static function savePost($post_id)
    {
        switch (get_post_type($post_id)) {
            case 'poll':
                $poll = new Poll($post_id);

                // Add items id
                $items = $poll->getField('items');
                foreach ($items as &$item) {
                    if (! $item['id']) {
                        $item['id'] = uniqid();
                    }
                }
                $poll->updateField('items', $items);

                // Items change

                $curr_items = $poll->getField('items');
                $prev_items = $poll->getField('prev_items');

                if (! is_array($prev_items)) {
                    $prev_invitees = $curr_items;
                }

                $added_items   = array_diff($curr_items, $prev_items);
                $removed_items = array_diff($prev_items, $curr_items);

                foreach ($added_items as $item) {
                    do_action('my_polls/item_added', $item, $poll);
                }

                foreach ($removed_items as $item) {
                    $poll->removeVotesByItem($item['id']);
                    do_action('my_polls/item_removed', $item, $poll);
                }

                $poll->updateField('prev_items', $curr_items);

                // Invitee change

                $curr_invitees = $poll->getField('invitees');
                $prev_invitees = $poll->getField('prev_invitees');

                if (! is_array($prev_invitees)) {
                    $prev_invitees = $curr_invitees;
                }

                $added_invitees   = array_diff($curr_invitees, $prev_invitees);
                $removed_invitees = array_diff($prev_invitees, $curr_invitees);

                foreach ($added_invitees as $user_id) {
                    do_action('my_polls/invitee_added', $user_id, $poll);
                }

                foreach ($removed_invitees as $user_id) {
                    $poll->setVotes($user_id, []);
                    do_action('my_polls/invitee_removed', $user_id, $poll);
                }

                $poll->updateField('prev_invitees', $curr_invitees);

                break;
        }
    }
}
