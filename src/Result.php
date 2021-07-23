<?php

namespace My\Polls;

use My\Polls\Posts\Poll;

class Result
{
    public static function init()
    {
    }
   
    public static function render($post = null)
    {
    }

    public static function process()
    {
        if (! wp_doing_ajax()) {
            return;
        }

        check_ajax_referer('form', MY_POLLS_NONCE_NAME);
    }

    public static function enqueueAssets()
    {
    }
}
