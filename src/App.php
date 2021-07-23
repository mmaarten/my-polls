<?php

namespace My\Polls;

use My\Polls\Posts\Poll;

class App
{
    /**
     * Init
     */
    public static function init()
    {
        array_map(function ($class) {
            call_user_func([__NAMESPACE__ . '\\' . $class, 'init']);
        }, [
            'Fields',
            'PostTypes',
            'Form',
            'Result',
        ]);

        add_action('init', [__CLASS__, 'loadTextdomain'], 0);
        add_action('acf/save_post', [__CLASS__, 'savePost']);
        add_action('template_redirect', [__CLASS__, 'processForm']);
        add_action('add_meta_boxes', [__CLASS__, 'addMetaBoxes']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'registerAssets'], 0);
        add_action('admin_enqueue_scripts', [__CLASS__, 'registerAssets'], 0);
        add_action('admin_enqueue_scripts', [__CLASS__, 'adminEnqueueAssets']);

        add_action('my_polls/item_added', function ($item, $poll) {
        }, 10, 2);

        add_action('my_polls/item_removed', function ($item, $poll) {
            $poll->removeVotesByItem($item['id']);
        }, 10, 2);

        add_action('my_polls/invitee_added', function ($user_id, $poll) {

            $email_sent = $poll->getField('email_sent');

            if (! is_array($email_sent)) {
                $email_sent = [];
            }

            if (isset($email_sent[$user_id])) {
                return;
            }

            $user = get_userdata($user_id);

            if (! $user) {
                return;
            }

            $to = $user->user_email;

            $subject = sprintf(__('You are invited for poll: %s', 'my-polls'), $poll->post_title);

            $message = '';

            wp_mail($to, $subject, $message);

            $email_sent[$user_id] = true;

            $poll->updateField('email_sent', $email_sent);

        }, 10, 2);

        add_action('my_polls/invitee_removed', function ($user_id, $poll) {
            $poll->setVotes($user_id, []);
        }, 10, 2);
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

    public static function renderResult($post)
    {
        $poll = new Poll($post);

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($poll->getItems() as $item) {
            $labels[] = $item['text'];
            $data[] = count($poll->getVotesByItem($item['id']));
            $colors[] = $item['color'];
        }

        $options = [
            'type' => $poll->getChartType(),
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data'            => $data,
                        'backgroundColor' => $colors,
                    ],
                ],
            ],
        ];

        printf('<canvas %s></canvas>', acf_esc_attr([
            'id'           => 'my-polls-result',
            'data-options' => $options,
        ]));
    }

    public static function registerAssets()
    {
        wp_register_script('my-polls-result-script', plugins_url('build/result.js', MY_POLLS_PLUGIN_FILE), ['jquery'], false, true);
    }

    public static function adminEnqueueAssets()
    {
        $screen = get_current_screen();

        if ($screen->id == 'poll') {
            wp_enqueue_style('my-polls-admin-style', plugins_url('build/admin-style.css', MY_POLLS_PLUGIN_FILE));
            wp_enqueue_script('my-polls-result-script');
        }
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-result-meta-box', __('Result', 'my-polls'), [__CLASS__, 'renderResult'], 'poll');
    }

    /**
     * Load textdomain
     */
    public static function loadTextdomain()
    {
        load_plugin_textdomain('my-polls', false, dirname(plugin_basename(MY_POLLS_PLUGIN_FILE)) . '/languages');
    }

    public static function savePost($post_id)
    {
        switch (get_post_type($post_id)) {
            case 'poll':
                $poll = new Poll($post_id);

                // Add items id
                $items = $poll->getField('items');
                foreach ($items as &$item) {
                    if (! $item['id']) {
                        $item['id'] = uniqid();
                    }
                }
                $poll->updateField('items', $items);

                // Items change

                $curr_items = $poll->getField('items');
                $prev_items = $poll->getField('prev_items');

                if (! is_array($prev_items)) {
                    $prev_invitees = $curr_items;
                }

                $added_items   = array_diff($curr_items, $prev_items);
                $removed_items = array_diff($prev_items, $curr_items);

                foreach ($added_items as $item) {
                    do_action('my_polls/item_added', $item, $poll);
                }

                foreach ($removed_items as $item) {
                    do_action('my_polls/item_removed', $item, $poll);
                }

                $poll->updateField('prev_items', $curr_items);

                // Invitee change

                $curr_invitees = $poll->getField('invitees');
                $prev_invitees = $poll->getField('prev_invitees');

                if (! is_array($prev_invitees)) {
                    $prev_invitees = $curr_invitees;
                }

                $added_invitees   = array_diff($curr_invitees, $prev_invitees);
                $removed_invitees = array_diff($prev_invitees, $curr_invitees);

                foreach ($added_invitees as $user_id) {
                    do_action('my_polls/invitee_added', $user_id, $poll);
                }

                foreach ($removed_invitees as $user_id) {
                    do_action('my_polls/invitee_removed', $user_id, $poll);
                }

                $poll->updateField('prev_invitees', $curr_invitees);

                break;
        }
    }
}
