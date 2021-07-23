<?php

namespace My\Polls;

class AdminMenu
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'addMenuPage']);
    }

    public static function addMenuPage()
    {
        add_menu_page(__('Polls', 'my-events'), __('Polls', 'my-events'), 'edit_posts', 'my-polls', '', 'dashicons-admin-post', 40);
    }
}
