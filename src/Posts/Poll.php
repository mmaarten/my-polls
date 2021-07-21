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

    public function getResults($item_id = null)
    {
        $results = $this->getField('results');

        if (! is_array($results)) {
            $results = [];
        }

        if (is_null($item_id)) {
            return $results;
        }

        if (isset($results[$item_id])) {
            return $results[$item_id];
        }

        return [];
    }

    public function hasResult($user_id, $item_id)
    {
        $item = $this->getItem($item_id);

        if (! $item) {
            return false;
        }

        return in_array($user_id, $this->getResults($item_id));
    }

    public function setResults($user_id, $item_ids)
    {
        if (! $this->isInvitee($user_id)) {
            return false;
        }

        $items = $this->getItems();

        $results = $this->getResults();

        foreach ($items as $item) {
            if (in_array($item['id'], $item_ids)) {
                $results[$item['id']][$user_id] = $user_id;
            } elseif (isset($results[$item['id']][$user_id])) {
                unset($results[$item['id']][$user_id]);
            }
        }

        return $this->updateField('results', $results);
    }

    public function isAnonymous()
    {
        return $this->getField('anonymous') ? true : false;
    }
}
