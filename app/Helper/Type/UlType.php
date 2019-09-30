<?php

namespace App\Helper\Type;

class UlType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'User ' . $this->message->getMessage() . ' removed';
    }
}