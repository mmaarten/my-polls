<?php

namespace My\Polls;

use My\Polls\Posts\Item;
use My\Polls\Posts\Poll;
use My\Polls\Posts\Invitee;

class AdminColumns
{
    const NO_VALUE = '–';

    /**
     * Init
     */
    public static function init()
    {
        add_filter('manage_poll_posts_columns', [__CLASS__, 'pollColumns']);
        add_action('manage_poll_posts_custom_column', [__CLASS__, 'pollColumnContent'], 10, 2);

        add_filter('manage_poll_invitee_posts_columns', [__CLASS__, 'inviteeColumns']);
        add_action('manage_poll_invitee_posts_custom_column', [__CLASS__, 'inviteeColumnContent'], 10, 2);

        add_filter('manage_poll_item_posts_columns', [__CLASS__, 'itemColumns']);
        add_action('manage_poll_item_posts_custom_column', [__CLASS__, 'itemColumnContent'], 10, 2);
    }

    /**
     * Poll columns
     *
     * @param array $columns
     * @return array
     */
    public static function pollColumns($columns)
    {
        return [
            'cb'       => $columns['cb'],
            'title'    => $columns['title'],
            'invitees' => __('Invitees', 'my-events'),
        ] + $columns;
    }

    /**
     * Poll column content
     *
     * @param string $column
     * @param int    $post_id
     * @return array
     */
    public static function pollColumnContent($column, $post_id)
    {
        $poll = new Poll($post_id);

        switch ($column) {
            case 'invitees':
                $invitees = $poll->getInviteesUsers(['fields' => 'ID', 'orderby' => 'display_name', 'order' => 'ASC']);
                echo $invitees ? Helpers::renderUsers($invitees) : esc_html(self::NO_VALUE);
                break;
        }
    }

    /**
     * Invitee columns
     *
     * @param array $columns
     * @return array
     */
    public static function inviteeColumns($columns)
    {
        return [
            'cb'              => $columns['cb'],
            'title'           => $columns['title'],
            'poll'            => __('Poll', 'my-events'),
            'user'            => __('User', 'my-events'),
            'invitation_sent' => __('Invitation sent', 'my-events'),
        ] + $columns;
    }

    /**
     * Invitee column content
     *
     * @param string $column
     * @param int    $post_id
     * @return array
     */
    public static function inviteeColumnContent($column, $post_id)
    {
        $invitee = new Invitee($post_id);

        switch ($column) {
            case 'poll':
                $poll = Helpers::renderPosts($invitee->getPoll());
                echo $poll ? $poll : esc_html(self::NO_VALUE);
                break;
            case 'user':
                $user = Helpers::renderUsers($invitee->getUser());
                echo $user ? $user : esc_html(self::NO_VALUE);
                break;
            case 'invitation_sent':
                echo Helpers::renderBoolean($invitee->getInvitationSent());
                break;
        }
    }

    /**
     * Item columns
     *
     * @param array $columns
     * @return array
     */
    public static function itemColumns($columns)
    {
        return [
            'cb'      => $columns['cb'],
            'title'   => $columns['title'],
            'poll'    => __('Poll', 'my-events'),
            'content' => __('Content', 'my-events'),
        ] + $columns;
    }

    /**
     * Item column content
     *
     * @param string $column
     * @param int    $post_id
     * @return array
     */
    public static function itemColumnContent($column, $post_id)
    {
        $item = new Item($post_id);

        switch ($column) {
            case 'poll':
                $poll = Helpers::renderPosts($item->getPoll());
                echo $poll ? $poll : esc_html(self::NO_VALUE);
                break;
            case 'content':
                $content = trim($item->getContent());
                echo $content ? esc_html($content) : esc_html(self::NO_VALUE);
                break;
        }
    }
}
