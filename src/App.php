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
            'PostTypes'
        ]);

        add_action('init', [__CLASS__, 'loadTextdomain'], 0);
        add_action('acf/save_post', [__CLASS__, 'savePost']);
        add_action('template_redirect', [__CLASS__, 'processForm']);
        add_action('add_meta_boxes', [__CLASS__,'addMetaBoxes']);

        add_filter('the_content', function ($return) {
            if (is_singular('poll')) {
                ob_start();
                self::form();
                $return .= ob_get_clean();
            }
            return $return;
        });
    }

    public static function addMetaBoxes()
    {
        add_meta_box('my-polls-results', __('Results', 'my-polls'), [__CLASS__, 'renderResults'], 'poll', 'side');
    }

    public static function renderResults($post)
    {
        $poll = new Poll($post);

        $items = $poll->getItems();

        foreach ($items as $item) {
            $user_ids = $poll->getResults($item['id']);
            $users = [];
            if ($user_ids) {
                $users = get_users(['include' => $user_ids]);
            }

            Helpers::adminNotice($item['text'], 'info', true);

            if ($users) {
                echo '<ul>';

                foreach ($users as $user) {
                    printf(
                        '<li><a href="%1$s">%2$s</a></li>',
                        esc_url(get_edit_user_link($user->ID)),
                        esc_html($user->display_name)
                    );
                }

                echo '</ul>';
            }
        }
    }

    public static function form($poll = null)
    {
        if (! is_user_logged_in()) {
            printf(
                '<div class="alert alert-danger" role="alert">%s</div>',
                esc_html__('', 'my-polls')
            );
            return;
        }

        $user_id = get_current_user_id();

        $poll = new Poll($poll);

        $invitee = $poll->getInvitee($user_id);

        if (! $invitee) {
            printf(
                '<div class="alert alert-danger" role="alert">%s</div>',
                esc_html__('', 'my-polls')
            );
            return;
        }

        $items = $poll->getItems();

        ?>

        <form id="my-polls-form" method="post">

            <?php wp_nonce_field('form', MY_POLLS_NONCE_NAME); ?>

            <input type="hidden" name="poll_id" value="<?php echo esc_attr($poll->ID); ?>">
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">

            <?php

            echo '<ul class="list-unstyled">';

            foreach ($items as $item) {
                printf(
                    '<li><label><input type="checkbox" name="items[]" value="%1$s"%3$s> %2$s</label></li>',
                    esc_attr($item['id']),
                    esc_html($item['text']),
                    checked($poll->hasResult($user_id, $item['id']), true, false)
                );
            }

            echo '</ul>';

            ?>

            <input type="submit" class="btn btn-primary" value="<?php esc_attr_e('Submit', 'my-polls'); ?>">

        </form>

        <?php
    }

    public static function processForm()
    {
        if (empty($_POST[MY_POLLS_NONCE_NAME])) {
            return;
        }

        if (! wp_verify_nonce($_POST[MY_POLLS_NONCE_NAME], 'form')) {
            return;
        }

        if (! is_user_logged_in()) {
            return;
        }

        $current_user_id = get_current_user_id();

        $poll_id = isset($_POST['poll_id']) ? $_POST['poll_id'] : 0;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        $items   = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [];

        if (! $poll_id || get_post_type($poll_id) != 'poll') {
            return;
        }

        if (! $user_id || ! get_userdata($user_id) || $user_id != $current_user_id) {
            return;
        }

        $poll = new Poll($poll_id);

        if (! $poll->isInvitee($user_id)) {
            return;
        }

        $poll->setResults($user_id, $items);
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

                $poll->updateField('prev_invitees', $invitees);

                break;
        }
    }
}
