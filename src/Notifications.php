<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Invitee;

class Notifications
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'maybeSendInvitation']);
    }

    public static function maybeSendInvitation()
    {
        $invitees = get_posts([
            'post_type'   => 'poll_invitee',
            'post_status' => 'publish',
            'numberposts' => 999,
            'meta_query'  => [
                'relation' => 'OR',
                [
                    'key'     => 'invitation_sent',
                    'compare' => '=',
                    'value'   => false,
                ],
                [
                    'key'     => 'invitation_sent',
                    'compare' => '!=',
                    'value'   => true,
                ],
                [
                    'key'     => 'invitation_sent',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ]);

        foreach ($invitees as $invitee) {
            $invitee = new Invitee($invitee);
            $poll_id = $invitee->getPoll();
            if ($poll_id && get_post_type($poll_id) && get_post_status($poll_id) == 'publish') {
                self::sendInvitation($invitee, $invitee->getUser(), new Poll($poll_id));
                $invitee->setInvitationSent(true);
            }
        }
    }

    public static function sendInvitation($invitee, $user_id, $poll)
    {
        $user = get_userdata($user_id);

        if (! $user) {
            return false;
        }

        $to = $user->user_email;

        $subject = sprintf(__('You are invited for poll: %s', 'my-polls'), $poll->post_title);

        $message = sprintf(
            '<p>%3$s <a href="%1$s">%2$s</a> %4$s</p>',
            get_permalink($poll->ID),
            esc_html($poll->post_title),
            esc_html__('You are invited for poll', 'my-polls'),
            esc_html__('Click the link to vote.', 'my-polls')
        );

        return self::sendNotification($to, $subject, $message);
    }

    public static function sendNotification($to, $subject, $message)
    {
        add_filter('wp_mail_content_type', [__CLASS__, 'wpMailContentType']);

        $send = wp_mail($to, $subject, $message);

        remove_filter('wp_mail_content_type', [__CLASS__, 'wpMailContentType']);

        return $send;
    }

    public static function wpMailContentType()
    {
        return 'text/html';
    }
}
