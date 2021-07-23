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
        //     return 'my-polls';
        // });
    }

    /**
     * Add poll fields
     */
    public static function addPollFields()
    {
        acf_add_local_field_group([
            'key'      => 'my_polls_poll_group',
            'title'    => __('General', 'my-polls'),
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

        // Description
        acf_add_local_field([
            'key'           => 'my_polls_poll_description_field',
            'label'         => __('Description', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'description',
            'type'          => 'textarea',
            'rows'          => 4,
            'new_lines'     => 'wpautop',
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Invitees
        acf_add_local_field([
            'key'           => 'my_polls_poll_invitees_field',
            'label'         => __('Invitees', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
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
            'label'         => __('Items', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'items',
            'type'          => 'repeater',
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Item ID
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_id_field',
            'label'         => __('ID', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'id',
            'type'          => 'text',
            'required'      => false,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Item Content
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_text_field',
            'label'         => __('Content', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'text',
            'type'          => 'text',
            'required'      => true,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Item Color
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_color_field',
            'label'         => __('Color', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'color',
            'type'          => 'color_picker',
            'required'      => true,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Chart type
        acf_add_local_field([
            'key'           => 'my_polls_poll_chart_type_field',
            'label'         => __('Chart type', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'chart_type',
            'type'          => 'select',
            'choices'       => [
                'bar'      => __('Bar', 'my-polls'),
                'doughnut' => __('Doughnut', 'my-polls'),
            ],
            'default_value' => 'bar',
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);
    }
}
