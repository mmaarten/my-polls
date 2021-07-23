<?php

namespace My\Polls\Posts;

class Item extends Post
{
    public function getPoll()
    {
        return $this->getField('poll');
    }

    public function setPoll($value)
    {
        return $this->updateField('poll', $value);
    }

    public function getContent()
    {
        return $this->getField('content');
    }

    public function setContent($value)
    {
        return $this->updateField('content', $value);
    }

    public function getColor()
    {
        return $this->getField('color');
    }

    public function setColor($value)
    {
        return $this->updateField('color', $value);
    }
}
