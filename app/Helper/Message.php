<?php

namespace App\Helper;

use App\Helper\Type\AbstractBaseType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Message
{
    protected $message;
    /**
     * @var \Parsedown
     */
    private $parsedown;
    private $baseUrl;

    public function __construct(\Parsedown $parsedown, $baseUrl, $message)
    {
        $this->message = $message;
        $this->parsedown = $parsedown;
        $this->baseUrl = $baseUrl;
    }

    public function hasName()
    {
        return property_exists($this->message->u, 'name');
    }

    public function getName()
    {
        return $this->message->u->name;
    }

    public function getBody()
    {
        if ($this->hasType()) {
            $type = $this->getType();
            if ($type) {
                return $type->render();
            }
        } else {
            return $this->parsedown->text($this->message->msg);
        }
    }

    public function hasType()
    {
        return property_exists($this->message, 't');
    }

    /**
     * @return AbstractBaseType|bool
     */
    public function getType()
    {
        $typeClass = strtolower($this->message->t);
        $type = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $typeClass)));
        $className = 'App\\Helper\\Type\\' . $type . 'Type';
        if (class_exists($className)) {
            return new $className($this);
        }
        Log::error('Unknown type: ' . $type, (array)$this->message);
        return false;
    }

    public function hasAttachment()
    {
        return property_exists($this->message, 'attachments');
    }

    /**
     * @return \Generator|Attachment
     */
    public function getAttachments()
    {
        foreach ($this->message->attachments as $attachment) {
            yield new Attachment($this->baseUrl, $attachment);
        }
    }

    public function getMessage()
    {
        return $this->message->msg;
    }

    public function getRole()
    {
        return $this->message->role;
    }

    public function getTimestamp()
    {
        return Carbon::parse($this->message->ts)->toDateTimeString();
    }
}