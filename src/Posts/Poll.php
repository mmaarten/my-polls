<?php

namespace My\Polls\Posts;

class Poll extends Post
{
    public function getDescription()
    {
        return $this->getField('description');
    }

    public function getInvitees($args = [])
    {
        return get_posts($args + [
            'post_type'    => 'poll_invitee',
            'post_status'  => 'publish',
            'numberposts'  => 999,
            'meta_key'     => 'poll',
            'meta_compare' => '=',
            'meta_value'   => $this->ID,
        ]);
    }

    public function getItems($args = [])
    {
        return get_posts($args + [
            'post_type'    => 'poll_item',
            'post_status'  => 'publish',
            'numberposts'  => 999,
            'meta_key'     => 'poll',
            'meta_compare' => '=',
            'meta_value'   => $this->ID,
        ]);
    }

    public function getInviteeByUser($user_id)
    {
        $invitee = current($this->getInvitees([
            'numberposts' => 1,
            'meta_query' => [
                [
                    'key'     => 'user',
                    'compare' => '=',
                    'value'   => $user_id,
                ],
            ],
        ]));

        return $invitee ? new Invitee($invitee) : null;
    }

    public function getInviteeByPost($invitee_id)
    {
        $invitee = current($this->getInvitees([
            'numberposts' => 1,
            'include'     => [$invitee_id],
        ]));

        return $invitee ? new Invitee($invitee) : null;
    }

    public function getInviteesUsers($args = [])
    {
        $invitees = $this->getInvitees();

        $user_ids = [];

        foreach ($invitees as $invitee) {
            $invitee = new Invitee($invitee);
            $user_ids[] = $invitee->getUser();
        }

        if (! $user_ids) {
            return [];
        }

        return get_users(['include' => $user_ids] + $args);
    }

    public function isInvitee($user_id)
    {
        return in_array($user_id, $this->getInviteesUsers(['fields' => 'ID']));
    }

    public function addInvitee($user_id)
    {
        $invitee = $this->getInviteeByUser($user_id);

        if ($invitee) {
            return $invitee->ID;
        }

        $post_id = wp_insert_post([
            'post_title'   => '',
            'post_content' => '',
            'post_type'    => 'poll_invitee',
            'post_status'  => 'publish',
        ]);

        $invitee = new Invitee($post_id);
        $invitee->setPoll($this->ID);
        $invitee->setUser($user_id);

        do_action('my_polls/invitee_added', $invitee, $invitee->getUser(), $this);

        return $invitee->ID;
    }

    public function removeInvitee($user_id)
    {
        $invitee = $this->getInviteeByUser($user_id);

        if (! $invitee) {
            return false;
        }

        return $this->removeInviteeByPost($invitee->ID);
    }

    public function removeInviteeByPost($invitee_id)
    {
        $invitee = $this->getInviteeByPost($invitee_id);

        if (! $invitee) {
            return false;
        }

        do_action('my_polls/invitee_removed', $invitee, $invitee->getUser(), $this);

        return wp_delete_post($invitee->ID, true);
    }

    public function setInvitees($user_ids)
    {
        $processed = [];

        foreach ((array) $user_ids as $user_id) {
            $processed[] = $this->addInvitee($user_id);
        }

        $delete = $this->getInvitees([
            'exclude'     => $processed,
            'post_status' => 'any',
        ]);

        foreach ($delete as $invitee) {
            $this->removeInviteeByPost($invitee->ID);
        }
    }

    public function setItems($items)
    {
        $processed = [];
        foreach ($items as $item) {
            $item = wp_parse_args($item, ['content' => '', 'color' => '']);
            if (! empty($item['id']) && get_post_type($item['id'])) {
                $post = new Item($item['id']);
                $post->setContent($item['content']);
                $post->setColor($item['color']);
                $processed[] = $post->ID;
            } else {
                $post_id = wp_insert_post([
                    'post_title'   => '',
                    'post_content' => '',
                    'post_status'  => 'publish',
                    'post_type'    => 'poll_item',
                ]);
                $post = new Item($post_id);
                $post->setContent($item['content']);
                $post->setColor($item['color']);
                $post->setPoll($this->ID);
                $processed[] = $post->ID;
            }
        }

        $delete = $this->getItems([
            'post_type'   => 'poll_item',
            'post_status' => 'any',
            'exclude'     => $processed,
        ]);

        foreach ($delete as $item) {
            wp_delete_post($item->ID, true);
        }
    }

    public function getVotes($args = [])
    {
        return get_posts([
            'post_type'    => 'poll_vote',
            'post_status'  => 'publish',
            'numberposts'  => 999,
            'fields'       => 'ids',
            'meta_key'     => 'poll',
            'meta_compare' => '=',
            'meta_value'   => $this->ID,
        ] + $args);
    }

    public function getVotesByItem($item_id)
    {
        return $this->getVotes([
            'meta_query'  => [
                [
                    'key'     => 'item',
                    'compare' => '=',
                    'value'   => $item_id,
                ],
            ],
        ]);
    }

    public function getVoteUsers($item_id, $args = [])
    {
        $votes = $this->getVotesByItem($item_id);

        $user_ids = [];

        foreach ($votes as $vote) {
            $vote = new Vote($vote);
            $invitee_id = $vote->getInvitee();
            if ($invitee_id && get_post_type($invitee_id)) {
                $invitee = new Invitee($invitee_id);
                $user_ids[] = $invitee->getUser();
            }
        }

        if (! $user_ids) {
            return [];
        }

        return get_users(['include' => $user_ids] + $args);
    }

    public function getEndDate($format = null)
    {
        $date = $this->getField('end_date');

        if (! $date) {
            return false;
        }

        if (! $format) {
            $format = get_option('date_format');
        }

        return date_i18n($format, strtotime($date));
    }

    public function hasEndDate()
    {
        return $this->getEndDate() ? true : false;
    }

    public function endDateReached()
    {
        if (! $this->hasEndDate()) {
            return false;
        }

        return strtotime($this->getEndDate('Y-m-d') . ' 23:59:59') < time();
    }

    public function areVotesAnonymous()
    {
        return $this->getField('anonymous_votes') ? true : false;
    }
}
