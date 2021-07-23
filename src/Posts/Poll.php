<?php

namespace My\Polls\Posts;

class Poll extends Post
{
    public function getInvitees($args = [])
    {
        $user_ids = $this->getField('invitees');

        if (! $user_ids || ! is_array($user_ids)) {
            return [];
        }

        return get_users(['include' => $user_ids] + $args);
    }

    public function getInvitee($user_id)
    {
        $invitees = $this->getInvitees();

        foreach ($invitees as $user) {
            if ($user_id == $user->ID) {
                return $user;
            }
        }

        return null;
    }

    public function isInvitee($user_id)
    {
        return $this->getInvitee($user_id) ? true : false;
    }

    public function getItems()
    {
        $items = $this->getField('items');

        if (! is_array($items)) {
            return [];
        }

        return $items;
    }

    public function getItem($id)
    {
        return current(wp_filter_object_list($this->getItems(), ['id' => $id]));
    }

    public function getChartType()
    {
        return $this->getField('chart_type');
    }

    public function getVotes()
    {
        $votes = $this->getField('votes');

        if (! is_array($votes)) {
            $votes = [];
        }

        return $votes;
    }

    public function getVotesByItem($item_id)
    {
        $votes = $this->getVotes();

        if (isset($votes[$item_id])) {
            return $votes[$item_id];
        }

        return null;
    }

    public function getVotesByUser($user_id)
    {
        $votes = $this->getVotes();

        $items = [];

        foreach ($votes as $item_id => $users) {
            if (isset($users[$user_id])) {
                $item = $this->getItem($item_id);
                if ($item) {
                    $items[$item['id']] = $item;
                }
            }
        }

        return $items;
    }

    public function removeVotesByItem($item_id)
    {
        $votes = $this->getVotes();

        if (isset($votes[$item_id])) {
            unset($votes[$item_id]);
        }

        return $this->updateField('votes', $votes);
    }

    public function removeVotesByUser($user_id)
    {
        $votes = $this->getVotes();

        foreach ($votes as $item_id => &$users) {
            if (isset($users[$user_id])) {
                unset($users[$user_id]);
            }
        }

        return $this->updateField('votes', $votes);
    }

    public function hasVoted($user_id, $item_id)
    {
        $votes = $this->getVotesByUser($user_id);

        return isset($votes[$item_id]) && $votes[$item_id];
    }

    public function setVotes($user_id, $item_ids)
    {
        $votes = $this->getVotes();

        foreach ($votes as $item_id => &$user_ids) {
            if (in_array($item_id, $item_ids)) {
                $user_ids[$user_id] = $user_id;
            } elseif (isset($user_ids[$user_id])) {
                unset($user_ids[$user_id]);
            }
        }

        error_log(print_r($votes, true));

        return $this->updateField('votes', $votes);
    }
}
