<?php

namespace My\Polls\Posts;

class Vote extends Post
{
    public function getPoll()
    {
        return $this->getField('poll');
    }

    public function setPoll($value)
    {
        return $this->updateField('poll', $value);
    }

    public function getInvitee()
    {
        return $this->getField('invitee');
    }

    public function setInvitee($value)
    {
        return $this->updateField('invitee', $value);
    }

    public function getItem()
    {
        return $this->getField('item');
    }

    public function setItem($value)
    {
        return $this->updateField('item', $value);
    }
}
