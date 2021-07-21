<?php

namespace My\Polls;

class Fields
{
    /**
     * Init
     */
    public static function init()
    {
        add_action('acf/init', [__CLASS__, 'addPollFields']);

        // add_filter('acf/settings/l10n_textdomain', function () {
        //     return 'my-events';
        // });
    }

    /**
     * Add poll fields
     */
    public static function addPollFields()
    {
        acf_add_local_field_group([
            'key'      => 'my_polls_poll_group',
            'title'    => __('General', 'my-events'),
            'fields'   => [],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'poll',
                    ],
                ],
            ],
        ]);

        // Invitees
        acf_add_local_field([
            'key'           => 'my_polls_poll_invitees_field',
            'label'         => __('Invitees', 'my-events'),
            'instructions'  => __('', 'my-events'),
            'name'          => 'invitees',
            'type'          => 'user',
            'return_format' => 'id',
            'multiple'      => true,
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Items
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_field',
            'label'         => __('Items', 'my-events'),
            'instructions'  => __('', 'my-events'),
            'name'          => 'items',
            'type'          => 'repeater',
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Item ID
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_id_field',
            'label'         => __('ID', 'my-events'),
            'instructions'  => __('', 'my-events'),
            'name'          => 'id',
            'type'          => 'text',
            'required'      => false,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Item Content
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_text_field',
            'label'         => __('Content', 'my-events'),
            'instructions'  => __('', 'my-events'),
            'name'          => 'text',
            'type'          => 'text',
            'required'      => true,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Anonymous
        acf_add_local_field([
            'key'           => 'my_polls_poll_anonymous_field',
            'label'         => __('Anonymous', 'my-events'),
            'instructions'  => __('No users are shown in the voting results.', 'my-events'),
            'name'          => 'anonymous',
            'type'          => 'true_false',
            'default_value' => false,
            'required'      => false,
            'parent'        => 'my_polls_poll_group',
        ]);
    }
}
