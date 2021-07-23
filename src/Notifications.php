<?php

namespace My\Polls;

use My\Polls\Posts\Poll;

class Notifications
{
    public static function init()
    {
        add_action('my_polls/invitee_added', [__CLASS__, 'sendInvitation'], 10, 2);
    }

    public static function sendInvitation($user_id, $poll)
    {
        $email_sent = $poll->getField('email_sent');

        if (! is_array($email_sent)) {
            $email_sent = [];
        }

        if (isset($email_sent[$user_id])) {
            return false;
        }

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

        $send = self::sendNotification($to, $subject, $message);

        $email_sent[$user_id] = true;

        $poll->updateField('email_sent', $email_sent);

        return $send;
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
