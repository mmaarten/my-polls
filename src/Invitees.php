<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Item;

class Invitees
{
    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'addMetaBoxes']);
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-invitees-meta-box', __('Invitees', 'my-polls'), [__CLASS__, 'render'], 'poll', 'side');
    }

    public static function render($post = null)
    {
        $poll = new Poll($post);

        $invitees = $poll->getInviteesUsers([
            'orderby' => 'display_name',
            'order'   => 'ASC',
        ]);

        if (! $invitees) {
            printf('<p>%s</p>', __('No invitees found.', 'my-polls'));
            return;
        }

        printf('<p>%s</p>', esc_html__('List of users who can vote:', 'my-polls'));

        echo '<p>';
        echo Helpers::renderUsers($invitees, ', ');
        echo '</p>';
    }
}
