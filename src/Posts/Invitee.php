<?php

namespace My\Polls\Posts;

class Invitee extends Post
{
    public function getPoll()
    {
        return $this->getField('poll');
    }

    public function setPoll($value)
    {
        return $this->updateField('poll', $value);
    }

    public function getUser()
    {
        return $this->getField('user');
    }

    public function setUser($value)
    {
        return $this->updateField('user', $value);
    }

    public function getInvitationSent()
    {
        return $this->getField('invitation_sent');
    }

    public function setInvitationSent($value)
    {
        return $this->updateField('invitation_sent', $value);
    }

    public function getVotes($args = [])
    {
        return get_posts([
            'post_type' => 'poll_vote',
            'post_status' => 'publish',
            'numberposts' => 999,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key'     => 'poll',
                    'compare' => '=',
                    'value'   => $this->getPoll(),
                ],
                [
                    'key'     => 'invitee',
                    'compare' => '',
                    'value'   => $this->ID,
                ],
            ],
        ] + $args);
    }

    public function getVoteByItem($item_id)
    {
        $vote = current($this->getVotes([
            'meta_key'     => 'item',
            'meta_compare' => ' = ',
            'meta_value'   => $item_id,
            'numberposts'  => 1,
        ]));

        return $vote ? new Vote($vote) : null;
    }

    public function getVoteByPost($vote_id)
    {
        $vote = current($this->getInvitees([
            'numberposts' => 1,
            'include'     => [$vote_id],
        ]));

        return $vote ? new Vote($vote) : null;
    }

    public function hasVote($item_id)
    {
        return $this->getVoteByItem($item_id) ? true : false;
    }

    public function addVote($item_id)
    {
        $vote = $this->getVoteByItem($item_id);

        if ($vote) {
            return $vote->ID;
        }

        $post_id = wp_insert_post([
            'post_title'   => '',
            'post_content' => '',
            'post_type'    => 'poll_vote',
            'post_status'  => 'publish',
        ]);

        $vote = new Vote($post_id);
        $vote->setInvitee($this->ID);
        $vote->setPoll($this->getPoll());
        $vote->setItem($item_id);

        return $vote->ID;
    }

    public function removeVote($item_id)
    {
        $vote = $this->getVoteByItem($item_id);

        if (! $vote) {
            return false;
        }

        return $this->removeVoteById($vote->ID);
    }

    public function removeVoteById($vote_id)
    {
        $vote = $this->getVoteByPost($vote_id);

        if (! $vote) {
            return false;
        }

        return wp_delete_post($vote->ID, true);
    }

    public function setVotes($item_ids)
    {
        $processed = [];

        foreach ((array) $item_ids as $item_id) {
            $processed[] = $this->addVote($item_id);
        }

        $delete = $this->getVotes([
            'exclude'     => $processed,
            'post_status' => 'any',
        ]);

        foreach ($delete as $vote) {
            $this->removeVoteByPost($vote->ID);
        }
    }
}
