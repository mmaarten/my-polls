<?php

namespace My\Polls;

use My\Polls\Posts\Post;
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
        add_action('wp_trash_post', [__CLASS__, 'trashPost']);
        add_action('before_delete_post', [__CLASS__, 'deletePost']);
        add_action('delete_user', [__CLASS__, 'deleteUser']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'adminEnqueueAssets']);

        add_filter('acf/load_value/key=my_polls_poll_invitees_field', [__CLASS__, 'populateInviteesField'], 10, 3);
        add_filter('acf/load_value/key=my_polls_poll_items_field', [__CLASS__, 'populateItemsField'], 10, 3);
    }

    public static function adminEnqueueAssets()
    {
        wp_enqueue_style('my-polls-admin-style', plugins_url('build/admin-style.css', MY_POLLS_PLUGIN_FILE));
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
                $invitees = $poll->getField('invitees', false);
                $poll->setInvitees($invitees);
                $poll->deleteField('invitees');

                // Items
                $items = $poll->getField('items');
                $poll->setItems($items);
                $poll->deleteField('items');

                break;
        }
    }

    /**
     * Trash post
     *
     * @param int $post_id
     */
    public static function trashPost($post_id)
    {
        switch (get_post_type($post_id)) {
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
                $invitee = new Invitee($post_id);
                $invitee->setVotes([]);
                break;
            case 'poll_item':
                // remove votes
                $votes = get_posts([
                    'post_type'    => 'poll_vote',
                    'post_status'  => 'any',
                    'numberposts'  => 999,
                    'fields'       => 'ids',
                    'meta_key'     => 'item',
                    'meta_compare' => '=',
                    'meta_value'   => $post_id,
                ]);
                foreach ($votes as $vote_id) {
                    wp_delete_post($vote_id, true);
                }
                break;
        }
    }

    /**
     * Delete user
     *
     * @param int $user_id
     */
    public static function deleteUser($user_id)
    {
        // Remove all user related invitees.
        $invitees = get_posts([
            'post_type'    => 'poll_invitee',
            'post_status'  => 'any',
            'numberposts'  => 999,
            'fields'       => 'ids',
            'meta_key'     => 'user',
            'meta_compare' => '=',
            'meta_value'   => $user_id,
        ]);
        foreach ($invitees as $invitee) {
            $invitee = new Invitee($invitee);
            $poll_id = $invitee->getPoll();
            if ($poll_id && get_post_type($poll_id)) {
                $poll = new Poll($poll_id);
                $poll->removeInvitee($user_id);
            } else {
                wp_delete_post($invitee->ID, true);
            }
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
