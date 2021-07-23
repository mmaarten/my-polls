<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Item;

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
            'Debug',
            'Fields',
            'PostTypes',
            'AdminMenu',
            'AdminColumns',
            'Form',
            'Result',
            'Notifications',
        ]);

        add_action('init', [__CLASS__, 'loadTextdomain'], 0);
        add_action('acf/save_post', [__CLASS__, 'savePost']);
        add_action('before_delete_post', [__CLASS__, 'deletePost']);

        add_filter('acf/load_value/key=my_polls_poll_invitees_field', [__CLASS__, 'populateInviteesField'], 10, 3);
        add_filter('acf/load_value/key=my_polls_poll_items_field', [__CLASS__, 'populateItemsField'], 10, 3);
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

                // Invitees
                $invitees = $poll->getField('invitees');
                $poll->setInvitees($invitees);
                $poll->deleteField('invitees');

                // Items
                $items = $poll->getField('items');
                $poll->setItems($items);
                $poll->deleteField('items');

                break;
        }
    }

    public static function deletePost($post_id)
    {
        switch (get_post_type($post_id)) {
            case 'poll':
                $poll = new Poll($post_id);
                $poll->setInvitees([]);
                $poll->setItems([]);
                break;
            case 'poll_invitee':
                // remove votes
                break;
            case 'poll_item':
                // remove votes
                break;
        }
    }

    public static function populateInviteesField($value, $post_id, $field)
    {
        if (get_post_type($post_id) != 'poll') {
            return $value;
        }

        if (did_action('acf/save_post')) {
            return $value;
        }

        $poll = new Poll($post_id);

        return $poll->getInviteesUsers(['fields' => 'ID']);
    }

    public static function populateItemsField($value, $post_id, $field)
    {
        if (get_post_type($post_id) != 'poll') {
            return $value;
        }

        if (did_action('acf/save_post')) {
            return $value;
        }

        $poll = new Poll($post_id);

        $value = [];
        foreach ($poll->getItems() as $item) {
            $item = new Item($item);
            $value[] = [
                'my_polls_poll_items_id_field'      => $item->ID,
                'my_polls_poll_items_content_field' => $item->getContent(),
                'my_polls_poll_items_color_field'   => $item->getColor(),
            ];
        }

        return $value;
    }
}
