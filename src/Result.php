<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Item;

class Result
{
    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'addMetaBoxes']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-result-meta-box', __('Result', 'my-polls'), [__CLASS__, 'render'], 'poll', 'side');
        add_meta_box('my-polls-result-chart-meta-box', __('Chart', 'my-polls'), [__CLASS__, 'renderChart'], 'poll', 'side');
    }

    public static function renderChart($post = null)
    {
        $poll = new Poll($post);

        $items = $poll->getItems();

        if (! $items) {
            printf('<p>%s</p>', __('No items found.', 'my-polls'));
            return;
        }

        $labels = [];
        $data   = [];
        $colors = [];

        foreach ($items as $item) {
            $item = new Item($item);
            $labels[] = $item->getContent();
            $data[] = count($poll->getVotesByItem($item->ID));
            $colors[] = $item->getColor();
        }

        if (! array_filter($data)) {
            printf('<p>%s</p>', __('No votes yet.', 'my-polls'));
            return;
        }

        printf('<canvas %s></canvas>', acf_esc_attr([
            'id' => 'my-polls-result-chart',
            'data-options' => [
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
            ],
        ]));
    }

    public static function render($post = null)
    {
        $poll = new Poll($post);

        $items = $poll->getItems();

        if (! $items) {
            printf('<p>%s</p>', __('No items found.', 'my-polls'));
            return;
        }

        $anonymous = $poll->areVotesAnonymous();

        $result = [];

        foreach ($items as $item) {
            $result[$item->ID] = $poll->getVoteUsers($item->ID, [
                'orderby' => 'display_name',
                'order'   => 'ASC',
            ]);
        }

        if (! array_filter($result)) {
            printf('<p>%s</p>', __('No votes yet.', 'my-polls'));
            return;
        }

        asort($result, SORT_NUMERIC);

        $result = array_reverse($result, true);

        echo '<ol>';

        foreach ($result as $item_id => $users) {
            $item = new Item($item_id);
            $count = count($users);
            printf(
                '<li><strong>%1$s</strong> (%2$d %3$s)',
                esc_html($item->getContent()),
                $count,
                esc_html(_n('vote', 'votes', $count, 'my-polls'))
            );

            if (! $poll->areVotesAnonymous()) {
                if ($users) {
                    printf('<p>%s</p>', Helpers::renderUsers($users));
                }
            }

            echo '</li>';
        }

        echo '</ol>';
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
        } elseif (is_singular('poll')) {
            wp_enqueue_script('my-polls-result-script');
        }
    }
}
