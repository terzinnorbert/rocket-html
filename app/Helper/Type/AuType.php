<?php

namespace App\Helper\Type;

class AuType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'User ' . $this->message->getMessage() . ' added';
    }
}