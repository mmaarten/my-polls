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

    public static function alert($message, $type = 'info', $return = false)
    {
        $return = sprintf(
            '<div class="alert alert-%1$s" role="alert">%2$s</div>',
            sanitize_html_class($type),
            esc_html($message),
        );

        if (! $return) {
            echo $return;
        }

        return $return;
    }
}
