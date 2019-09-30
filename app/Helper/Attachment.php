<?php

namespace App\Helper;

class Attachment
{
    private $attachment;
    private $baseUrl;

    public function __construct($baseUrl, $attachment)
    {
        $this->attachment = $attachment;
        $this->baseUrl = $baseUrl;
    }

    public function render()
    {
        if ($this->isImage()) {
            return view('message.attachment.image', [
                'attachment' => $this,
            ]);
        } elseif ($this->isLink()) {
            return view('message.attachment.link', [
                'attachment' => $this,
            ]);
        }
    }

    protected function isImage()
    {
        return property_exists($this->attachment, 'image_preview');
    }

    protected function isLink()
    {
        return property_exists($this->attachment, 'title_link');
    }

    public function hasDescription()
    {
        return property_exists($this->attachment, 'description');
    }

    public function getDescription()
    {
        return $this->attachment->description;
    }

    public function getTitle()
    {
        return $this->attachment->title;
    }

    public function getText()
    {
        return $this->attachment->text;
    }

    public function getUrl()
    {
        if ($this->isImage()) {
            return File::url($this->baseUrl, $this->attachment->image_url);
        }
        return File::url($this->baseUrl, $this->attachment->title_link);
    }

    public function hasTitleLink()
    {
        return property_exists($this->attachment, 'title_link');
    }

    public function getTitleLink()
    {
        return $this->attachment->title_link;
    }

    public function getSafeTitleLink()
    {
        return File::safeName($this->getTitleLink());
    }
}