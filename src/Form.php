<?php

namespace My\Polls;

use My\Polls\Posts\Poll;
use My\Polls\Posts\Item;

class Form
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

        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueAssets']);
        add_action('wp_ajax_my_polls_process_form', [__CLASS__, 'process']);
        add_action('wp_ajax_noprive_my_polls_process_form', [__CLASS__, 'process']);
    }

    public static function render($post = null)
    {
        if (get_post_type($post) != 'poll') {
            Helpers::alert(__('Invalid poll.', 'my-polls'), 'danger');
            return;
        }

        if (! is_user_logged_in()) {
            Helpers::alert(__('You need to login in order to vote.', 'my-polls'), 'danger');
            return;
        }

        $user_id = get_current_user_id();

        $poll = new Poll($post);

        if ($poll->endDateReached()) {
            Helpers::alert(__('The voting period is over.', 'my-polls'), 'danger');
            return;
        }

        $invitee = $poll->getInviteeByUser($user_id);

        if (! $invitee) {
            Helpers::alert(__('You need an invitation in order to vote.', 'my-polls'), 'danger');
            return;
        }

        if ($poll->areVotesAnonymous()) {
            Helpers::alert(__('Votes are anonymous.', 'my-polls'), 'info');
        }

        if ($poll->hasEndDate()) {
            Helpers::alert(sprintf(__('Voting is possible until: %s.', 'my-polls'), $poll->getEndDate()), 'warning');
        }

        ?>

        <form id="my-polls-form">

            <?php wp_nonce_field('form', MY_POLLS_NONCE_NAME); ?>

            <input type="hidden" name="action" value="my_polls_process_form">
            <input type="hidden" name="poll" value="<?php echo esc_attr($poll->ID); ?>">
            <input type="hidden" name="user" value="<?php echo esc_attr($user_id); ?>">

            <?php

            echo '<ul class="list-unstyled">';

            foreach ($poll->getItems() as $item) {
                $item = new Item($item);
                printf(
                    '<li><label><input type="checkbox" name="items[]" value="%1$s"%3$s> %2$s</label></li>',
                    esc_attr($item->ID),
                    esc_html($item->getContent()),
                    checked($invitee->hasVote($item->ID), true, false)
                );
            }

            echo '</ul>';

            ?>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="<?php esc_attr_e('Submit', 'my-polls'); ?>">
            </div>

            <div class="my-polls-output"></div>

        </form>

        <?php
    }

    public static function process()
    {
        if (! wp_doing_ajax()) {
            return;
        }

        check_ajax_referer('form', MY_POLLS_NONCE_NAME);

        $poll_id = isset($_POST['poll']) ? $_POST['poll'] : 0;
        $user_id = isset($_POST['user']) ? $_POST['user'] : 0;
        $items   = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [];

        if (! $poll_id || get_post_type($poll_id) != 'poll') {
            wp_send_json(Helpers::alert(__('Invalid poll.', 'my-polls'), 'danger', false));
        }

        $poll = new Poll($poll_id);

        if ($poll->endDateReached()) {
            wp_send_json(Helpers::alert(__('The voting period is over.', 'my-polls'), 'danger', false));
            return;
        }

        if (! $user_id || ! get_userdata($user_id)) {
            wp_send_json(Helpers::alert(__('Invalid user.', 'my-polls'), 'danger', false));
        }

        if (! is_user_logged_in()) {
            wp_send_json(Helpers::alert(__('You need to login in order to vote.', 'my-polls'), 'danger', false));
        }

        if (get_current_user_id() != $user_id) {
            wp_send_json(Helpers::alert(__('You cannot vote for someone else.', 'my-polls'), 'danger', false));
        }

        $invitee = $poll->getInviteeByUser($user_id);

        if (! $invitee) {
            wp_send_json(Helpers::alert(__('You need an invitation in order to vote.', 'my-polls'), 'danger', false));
            return;
        }

        $invitee->setVotes($items);

        wp_send_json(Helpers::alert(__('Your votes have been saved.', 'my-polls'), 'success', false));
    }

    public static function enqueueAssets()
    {
        wp_register_script('my-polls-form-script', plugins_url('build/form.js', MY_POLLS_PLUGIN_FILE), ['jquery'], false, true);

        wp_localize_script('my-polls-form-script', 'MyPolls', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);

        // Temp
        if (is_singular('poll')) {
            wp_enqueue_script('my-polls-form-script');
        }
    }
}
