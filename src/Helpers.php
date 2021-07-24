<?php

namespace My\Polls;

class Helpers
{
    public static function adminNotice($message, $type = 'info', $inline = false)
    {
        printf(
            '<div class="notice notice-%1$s%3$s"><p>%2$s</p></div>',
            sanitize_html_class($type),
            esc_html($message),
            $inline ? ' inline' : ''
        );
    }

    public static function alert($message, $type = 'info', $echo = true)
    {
        $return = sprintf(
            '<div class="alert alert-%1$s" role="alert">%2$s</div>',
            sanitize_html_class($type),
            esc_html($message)
        );

        if ($echo) {
            echo $return;
        }

        return $return;
    }

    public static function renderPosts($post_ids, $separator = ', ')
    {
        $return = [];

        foreach ((array) $post_ids as $post_id) {
            if ($post_id && get_post_type($post_id)) {
                $return[] = sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url(get_edit_post_link($post_id)),
                    esc_html(get_the_title($post_id))
                );
            }
        }

        return implode($separator, $return);
    }

    public static function renderUsers($user_ids, $separator = ', ')
    {
        $return = [];

        foreach ((array) $user_ids as $user_id) {
            $user = is_a($user_id, 'WP_User') ? $user_id : get_userdata($user_id);
            if ($user) {
                if (current_user_can('edit_users')) {
                    $return[] = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url(get_edit_user_link($user->ID)),
                        esc_html($user->display_name)
                    );
                } else {
                    $return[] = esc_html($user->display_name);
                }
            }
        }

        return implode($separator, $return);
    }

    /**
     * Render boolean
     *
     * @param bool $value
     * @return string
     */
    public static function renderBoolean($value)
    {
        return sprintf(
            '<span class="dashicons-before dashicons-%1$s" title="%2$s"></span>',
            $value ? 'yes' : 'no-alt',
            $value ? esc_attr__('yes', 'my-polls') : esc_attr__('no', 'my-polls')
        );
    }
}
