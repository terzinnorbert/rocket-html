<?php

namespace App\Helper\Type;

class RuType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'User ' . $this->message->getMessage() . ' left';
    }
}