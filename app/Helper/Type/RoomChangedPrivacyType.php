<?php

namespace App\Helper\Type;

class RoomChangedPrivacyType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'Room type changed to: ' . $this->message->getMessage();
    }
}