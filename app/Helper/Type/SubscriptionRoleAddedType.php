<?php

namespace App\Helper\Type;

class SubscriptionRoleAddedType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'User ' . $this->message->getMessage() . ' was set ' . $this->message->getRole();
    }
}