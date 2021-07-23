<?php

namespace My\Polls;

use My\Polls\Posts\Poll;

class Result
{
    public static function init()
    {
         // Temp
         add_filter('the_content', function ($return) {
            if (is_singular('poll')) {
                ob_start();
                self::render();
                $return .= ob_get_clean();
            }
            return $return;
        });

        add_action('add_meta_boxes', [__CLASS__, 'addMetaBoxes']);
        add_action('wp_ajax_my_polls_result_process', [__CLASS__, 'process']);
        add_action('wp_ajax_nopriv_my_polls_result_process', [__CLASS__, 'process']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-result-meta-box', __('Result', 'my-polls'), [__CLASS__, 'render'], 'poll');
    }

    public static function render($post = null)
    {
        if (get_post_type($post) != 'poll') {
            Helpers::alert(__('Invalid poll.', 'my-polls'), 'danger');
            return;
        }

        $poll = new Poll($post);

        printf('<canvas %s></canvas>', acf_esc_attr([
            'id'             => 'my-polls-result',
            'data-action'    => 'my_polls_result_process',
            'data-poll'      => $poll->ID,
            'data-noncename' => MY_POLLS_NONCE_NAME,
            'data-nonce'     => wp_create_nonce('result'),
        ]));
    }

    public static function process()
    {
        if (! wp_doing_ajax()) {
            return;
        }

        check_ajax_referer('result', MY_POLLS_NONCE_NAME);

        $poll_id = isset($_POST['poll']) ? $_POST['poll'] : 0;

        if (! $poll_id || get_post_type($poll_id) != 'poll') {
            wp_send_json_error(Helpers::alert(__('Invalid poll.', 'my-polls'), 'danger', true));
        }

        $poll = new Poll($poll_id);

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($poll->getItems() as $item) {
            $labels[] = $item['text'];
            $data[] = count($poll->getVotesByItem($item['id']));
            $colors[] = $item['color'];
        }

        $options = [
            'type' => 'doughnut',
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

        wp_send_json_success($options);
    }

    public static function enqueueAssets()
    {
        wp_register_script('my-polls-result-script', plugins_url('build/result.js', MY_POLLS_PLUGIN_FILE), ['jquery'], false, true);

        wp_localize_script('my-polls-result-script', 'MyPolls', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);

        if (is_admin()) {
            $screen = get_current_screen();
            if ($screen->id == 'poll') {
                wp_enqueue_script('my-polls-result-script');
            }
        } else {
            // Temp
            if (is_singular('poll')) {
                wp_enqueue_script('my-polls-result-script');
            }
        }
    }
}
