<?php


namespace App\Helper;


use App\Client\Chat;

class Export
{
    /**
     * @var Chat
     */
    private $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function downloadAsZip($url, $username, $password)
    {
        $this->chat->connect($url, $username, $password);
        $this->chat->downloadChannelsMessages();
        $this->chat->downloadGroupsMessages();
        $this->chat->downloadImsMessages();
        $path = $this->chat->compressDownloadedMessages();
        $this->chat->deleteDownloadedMessages();

        return $path;
    }
}