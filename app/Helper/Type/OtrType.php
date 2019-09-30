<?php

namespace App\Helper\Type;

class OtrType extends AbstractBaseType
{
    protected function getMessage()
    {
        return '...OTR encrypted message...';
    }
}