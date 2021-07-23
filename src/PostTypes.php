<?php

namespace My\Polls;

class PostTypes
{
    /**
     * Init
     */
    public static function init()
    {
        add_action('init', [__CLASS__, 'registerPostTypes']);
    }

    /**
     * Register post types
     */
    public static function registerPostTypes()
    {
        register_post_type('poll', [
            'labels'             => [
                'name'                  => _x('Polls', 'Post type general name', 'my-polls'),
                'singular_name'         => _x('Poll', 'Post type singular name', 'my-polls'),
                'menu_name'             => _x('Polls', 'Admin Menu text', 'my-polls'),
                'name_admin_bar'        => _x('Poll', 'Add New on Toolbar', 'my-polls'),
                'add_new'               => __('Add New', 'my-polls'),
                'add_new_item'          => __('Add New Poll', 'my-polls'),
                'new_item'              => __('New Poll', 'my-polls'),
                'edit_item'             => __('Edit Poll', 'my-polls'),
                'view_item'             => __('View Poll', 'my-polls'),
                'all_items'             => __('Polls', 'my-polls'),
                'search_items'          => __('Search Polls', 'my-polls'),
                'parent_item_colon'     => __('Parent Polls:', 'my-polls'),
                'not_found'             => __('No polls found.', 'my-polls'),
                'not_found_in_trash'    => __('No polls found in Trash.', 'my-polls'),
                'featured_image'        => _x('Poll Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'my-polls'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'archives'              => _x('Poll archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'my-polls'),
                'insert_into_item'      => _x('Insert into poll', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'my-polls'),
                'uploaded_to_this_item' => _x('Uploaded to this poll', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'my-polls'),
                'filter_items_list'     => _x('Filter polls list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'my-polls'),
                'items_list_navigation' => _x('Polls list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'my-polls'),
                'items_list'            => _x('Polls list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'my-polls'),
            ],
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'my-polls',
            'query_var'          => true,
            'rewrite'            => ['poll'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'thumbnail', 'comments'],
        ]);

        register_post_type('poll_invitee', [
            'labels'             => [
                'name'                  => _x('Invitees', 'Post type general name', 'my-polls'),
                'singular_name'         => _x('Invitee', 'Post type singular name', 'my-polls'),
                'menu_name'             => _x('Invitees', 'Admin Menu text', 'my-polls'),
                'name_admin_bar'        => _x('Invitee', 'Add New on Toolbar', 'my-polls'),
                'add_new'               => __('Add New', 'my-polls'),
                'add_new_item'          => __('Add New Invitee', 'my-polls'),
                'new_item'              => __('New Invitee', 'my-polls'),
                'edit_item'             => __('Edit Invitee', 'my-polls'),
                'view_item'             => __('View Invitee', 'my-polls'),
                'all_items'             => __('Invitees', 'my-polls'),
                'search_items'          => __('Search Invitees', 'my-polls'),
                'parent_item_colon'     => __('Parent Invitees:', 'my-polls'),
                'not_found'             => __('No invitees found.', 'my-polls'),
                'not_found_in_trash'    => __('No invitees found in Trash.', 'my-polls'),
                'featured_image'        => _x('Invitee Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'my-polls'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'archives'              => _x('Invitee archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'my-polls'),
                'insert_into_item'      => _x('Insert into invitee', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'my-polls'),
                'uploaded_to_this_item' => _x('Uploaded to this invitee', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'my-polls'),
                'filter_items_list'     => _x('Filter invitees list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'my-polls'),
                'items_list_navigation' => _x('Invitees list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'my-polls'),
                'items_list'            => _x('Invitees list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'my-polls'),
            ],
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'my-polls',
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title'],
        ]);

        register_post_type('poll_item', [
            'labels'             => [
                'name'                  => _x('Items', 'Post type general name', 'my-polls'),
                'singular_name'         => _x('Item', 'Post type singular name', 'my-polls'),
                'menu_name'             => _x('Items', 'Admin Menu text', 'my-polls'),
                'name_admin_bar'        => _x('Item', 'Add New on Toolbar', 'my-polls'),
                'add_new'               => __('Add New', 'my-polls'),
                'add_new_item'          => __('Add New Item', 'my-polls'),
                'new_item'              => __('New Item', 'my-polls'),
                'edit_item'             => __('Edit Item', 'my-polls'),
                'view_item'             => __('View Item', 'my-polls'),
                'all_items'             => __('Items', 'my-polls'),
                'search_items'          => __('Search Items', 'my-polls'),
                'parent_item_colon'     => __('Parent Items:', 'my-polls'),
                'not_found'             => __('No items found.', 'my-polls'),
                'not_found_in_trash'    => __('No items found in Trash.', 'my-polls'),
                'featured_image'        => _x('Item Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'my-polls'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'archives'              => _x('Item archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'my-polls'),
                'insert_into_item'      => _x('Insert into item', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'my-polls'),
                'uploaded_to_this_item' => _x('Uploaded to this item', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'my-polls'),
                'filter_items_list'     => _x('Filter items list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'my-polls'),
                'items_list_navigation' => _x('Items list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'my-polls'),
                'items_list'            => _x('Items list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'my-polls'),
            ],
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'my-polls',
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title'],
        ]);

        register_post_type('poll_vote', [
            'labels'             => [
                'name'                  => _x('Votes', 'Post type general name', 'my-polls'),
                'singular_name'         => _x('Vote', 'Post type singular name', 'my-polls'),
                'menu_name'             => _x('Votes', 'Admin Menu text', 'my-polls'),
                'name_admin_bar'        => _x('Vote', 'Add New on Toolbar', 'my-polls'),
                'add_new'               => __('Add New', 'my-polls'),
                'add_new_item'          => __('Add New Vote', 'my-polls'),
                'new_item'              => __('New Vote', 'my-polls'),
                'edit_item'             => __('Edit Vote', 'my-polls'),
                'view_item'             => __('View Vote', 'my-polls'),
                'all_items'             => __('Votes', 'my-polls'),
                'search_items'          => __('Search Votes', 'my-polls'),
                'parent_item_colon'     => __('Parent Votes:', 'my-polls'),
                'not_found'             => __('No votes found.', 'my-polls'),
                'not_found_in_trash'    => __('No votes found in Trash.', 'my-polls'),
                'featured_image'        => _x('Vote Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'my-polls'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'my-polls'),
                'archives'              => _x('Vote archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'my-polls'),
                'insert_into_item'      => _x('Insert into vote', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'my-polls'),
                'uploaded_to_this_item' => _x('Uploaded to this vote', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'my-polls'),
                'filter_items_list'     => _x('Filter votes list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'my-polls'),
                'items_list_navigation' => _x('Votes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'my-polls'),
                'items_list'            => _x('Votes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'my-polls'),
            ],
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'my-polls',
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title'],
        ]);
    }
}
