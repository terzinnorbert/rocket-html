<?php

namespace App\Helper\Type;

class RType extends AbstractBaseType
{
    public function render()
    {
        return view('message.type.code', [
            'message' => 'Room name changed to:',
            'code' => $this->getMessage(),
        ]);
    }
}