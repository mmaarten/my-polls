<?php

namespace My\Polls;

use My\Polls\Posts\Post;
use My\Polls\Posts\Item;
use My\Polls\Posts\Poll;
use My\Polls\Posts\Invitee;
use My\Polls\Posts\Vote;

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

        add_filter('manage_poll_vote_posts_columns', [__CLASS__, 'voteColumns']);
        add_action('manage_poll_vote_posts_custom_column', [__CLASS__, 'voteColumnContent'], 10, 2);
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
            'invitees' => __('Invitees', 'my-polls'),
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
            'poll'            => __('Poll', 'my-polls'),
            'user'            => __('User', 'my-polls'),
            'invitation_sent' => __('Invitation sent', 'my-polls'),
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
            'poll'    => __('Poll', 'my-polls'),
            'content' => __('Content', 'my-polls'),
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

    /**
     * Vote columns
     *
     * @param array $columns
     * @return array
     */
    public static function voteColumns($columns)
    {
        return [
            'cb'      => $columns['cb'],
            'title'   => $columns['title'],
            'poll'    => __('Poll', 'my-polls'),
            'invitee' => __('Invitee', 'my-polls'),
            'item'    => __('Item', 'my-polls'),
        ] + $columns;
    }

    /**
     * Vote column content
     *
     * @param string $column
     * @param int    $post_id
     * @return array
     */
    public static function voteColumnContent($column, $post_id)
    {
        $vote = new Vote($post_id);

        switch ($column) {
            case 'poll':
                $poll = Helpers::renderPosts($vote->getPoll());
                echo $poll ? $poll : esc_html(self::NO_VALUE);
                break;
            case 'invitee':
                $invitee = Helpers::renderPosts($vote->getInvitee());
                echo $invitee ? $invitee : esc_html(self::NO_VALUE);
                break;
            case 'item':
                $item = Helpers::renderPosts($vote->getItem());
                echo $item ? $item : esc_html(self::NO_VALUE);
                break;
        }
    }
}
