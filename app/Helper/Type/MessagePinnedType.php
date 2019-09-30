<?php

namespace App\Helper\Type;

class MessagePinnedType extends AbstractBaseType
{
    public function render()
    {
        return view('message.type.code', [
            'message' => 'Message has been pinned:',
            'code' => $this->message->getAttachments()->current()->getText(),
        ]);
    }
}