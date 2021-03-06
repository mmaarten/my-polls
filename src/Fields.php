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
        add_action('acf/init', [__CLASS__, 'addInviteeFields']);
        add_action('acf/init', [__CLASS__, 'addItemFields']);
        add_action('acf/init', [__CLASS__, 'addVoteFields']);

        add_filter('acf/settings/l10n_textdomain', function ($return) {

            $post_types = ['poll', 'poll_invitee', 'poll_item', 'poll_vote'];

            if (is_admin() && function_exists('get_current_screen')) {
                $screen = get_current_screen();

                if ($screen && in_array($screen->id, $post_types)) {
                    return 'my-polls';
                }
            }

            return $return;
        }, PHP_INT_MAX);
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
            'instructions'  => __('A brief description about this poll.', 'my-polls'),
            'name'          => 'description',
            'type'          => 'textarea',
            'rows'          => 4,
            'new_lines'     => 'wpautop',
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Anyone can vote
        acf_add_local_field([
            'key'           => 'my_polls_poll_anyone_can_vote_field',
            'label'         => __('Anyone can vote', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'anyone_can_vote',
            'type'          => 'true_false',
            'default_value' => true,
            'required'      => false,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Invitees
        acf_add_local_field([
            'key'           => 'my_polls_poll_invitees_field',
            'label'         => __('Invitees', 'my-polls'),
            'instructions'  => __('A list of users who are able to vote.', 'my-polls'),
            'name'          => 'invitees',
            'type'          => 'user',
            'return_format' => 'id',
            'multiple'      => true,
            'required'      => true,
            'parent'        => 'my_polls_poll_group',
            'conditional_logic' => [
                [
                    [
                        'field'    => 'my_polls_poll_anyone_can_vote_field',
                        'operator' => '==',
                        'value'    => false
                    ],
                ],
            ],
        ]);

        // Items
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_field',
            'label'         => __('Items', 'my-polls'),
            'instructions'  => __('The items to vote for.', 'my-polls'),
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
            'key'           => 'my_polls_poll_items_content_field',
            'label'         => __('Content', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'content',
            'type'          => 'text',
            'required'      => true,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // Item Color
        acf_add_local_field([
            'key'           => 'my_polls_poll_items_color_field',
            'label'         => __('Color', 'my-polls'),
            'instructions'  => __('The color for the chart.', 'my-polls'),
            'name'          => 'color',
            'type'          => 'color_picker',
            'required'      => true,
            'parent'        => 'my_polls_poll_items_field',
        ]);

        // End date
        acf_add_local_field([
            'key'           => 'my_polls_poll_end_date_field',
            'label'         => __('End date', 'my-polls'),
            'instructions'  => __('The date the voting ends.', 'my-polls'),
            'name'          => 'end_date',
            'type'           => 'date_time_picker',
            'display_format' => get_option('date_format'),
            'return_format'  => 'Y-m-d',
            'first_day'      => get_option('start_of_week', 0),
            'default_value'  => '',
            'required'       => false,
            'parent'        => 'my_polls_poll_group',
        ]);

        // Anonymous votes
        acf_add_local_field([
            'key'           => 'my_polls_poll_anonymous_votes_field',
            'label'         => __('Anonymous voting', 'my-polls'),
            'instructions'  => __('Names will not be displayed in the results.', 'my-polls'),
            'name'          => 'anonymous_votes',
            'type'          => 'true_false',
            'default_value' => false,
            'required'      => false,
            'parent'        => 'my_polls_poll_group',
        ]);
    }

    /**
     * Add invitee fields
     */
    public static function addInviteeFields()
    {
        acf_add_local_field_group([
            'key'      => 'my_polls_invitee_group',
            'title'    => __('General', 'my-polls'),
            'fields'   => [],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'poll_invitee',
                    ],
                ],
            ],
        ]);

        // Poll
        acf_add_local_field([
            'key'           => 'my_polls_invitee_event_field',
            'label'         => __('Poll', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'poll',
            'type'          => 'post_object',
            'post_type'     => 'poll',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_invitee_group',
        ]);

        // User
        acf_add_local_field([
            'key'           => 'my_polls_invitee_user_field',
            'label'         => __('User', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'user',
            'type'          => 'user',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_invitee_group',
        ]);

        // Invitation sent
        acf_add_local_field([
            'key'           => 'my_polls_invitee_invitation_sent_field',
            'label'         => __('Invitation sent', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'invitation_sent',
            'type'          => 'true_false',
            'default_value' => false,
            'required'      => false,
            'parent'        => 'my_polls_invitee_group',
        ]);
    }

    /**
     * Add item fields
     */
    public static function addItemFields()
    {
        acf_add_local_field_group([
            'key'      => 'my_polls_item_group',
            'title'    => __('General', 'my-polls'),
            'fields'   => [],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'poll_item',
                    ],
                ],
            ],
        ]);

        // Poll
        acf_add_local_field([
            'key'           => 'my_polls_item_poll_field',
            'label'         => __('Poll', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'poll',
            'type'          => 'post_object',
            'post_type'     => 'poll',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_item_group',
        ]);

        // User
        acf_add_local_field([
            'key'           => 'my_polls_item_content_field',
            'label'         => __('content', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'content',
            'type'          => 'text',
            'required'      => true,
            'parent'        => 'my_polls_item_group',
        ]);
    }

    /**
     * Add vote fields
     */
    public static function addVoteFields()
    {
        acf_add_local_field_group([
            'key'      => 'my_polls_vote_group',
            'title'    => __('General', 'my-polls'),
            'fields'   => [],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'poll_vote',
                    ],
                ],
            ],
        ]);

        // Poll
        acf_add_local_field([
            'key'           => 'my_polls_vote_poll_field',
            'label'         => __('Poll', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'poll',
            'type'          => 'post_object',
            'post_type'     => 'poll',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_vote_group',
        ]);

        // Invitee
        acf_add_local_field([
            'key'           => 'my_polls_vote_invitee_field',
            'label'         => __('Invitee', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'invitee',
            'type'          => 'post_object',
            'post_type'     => 'poll_invitee',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_vote_group',
        ]);

        // Item
        acf_add_local_field([
            'key'           => 'my_polls_vote_item_field',
            'label'         => __('Item', 'my-polls'),
            'instructions'  => __('', 'my-polls'),
            'name'          => 'item',
            'type'          => 'post_object',
            'post_type'     => 'poll_item',
            'return_format' => 'id',
            'multiple'      => false,
            'required'      => true,
            'parent'        => 'my_polls_vote_group',
        ]);
    }
}
