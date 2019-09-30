<?php

namespace App\Helper\Type;

class UjType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'User ' . $this->message->getMessage() . ' has joined';
    }
}