<?php

namespace App\Helper\Type;

class RoomChangedTopicType extends AbstractBaseType
{
    public function render()
    {
        return view('message.type.code', [
            'message' => 'Room topic changed to:',
            'code' => $this->getMessage(),
        ]);
    }
}