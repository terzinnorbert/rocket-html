<?php

namespace App\Helper\Type;

use App\Helper\Message;

abstract class AbstractBaseType
{

    /**
     * @var Message
     */
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return view('message.type.text', [
            'message' => $this->getMessage(),
        ]);
    }

    protected function getMessage()
    {
        return $this->message->getMessage();
    }
}