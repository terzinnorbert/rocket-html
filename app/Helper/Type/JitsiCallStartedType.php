<?php

namespace App\Helper\Type;

class JitsiCallStartedType extends AbstractBaseType
{
    protected function getMessage()
    {
        return 'Started a Video Call';
    }
}