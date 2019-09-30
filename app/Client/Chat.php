<?php

namespace App\Client;

use App\Exceptions\LoginErrorException;
use App\Helper\File;
use App\Helper\Message;
use App\Helper\Url;
use App\Helper\Zip;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Parsedown;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Chat
{
    const LIST_TYPE_CHANNEL = 'channels';
    const LIST_TYPE_IM = 'im';
    const LIST_TYPE_GROUP = 'groups';
    const LIST_TYPE_RESPONSE = [
        self::LIST_TYPE_CHANNEL => self::LIST_TYPE_CHANNEL,
        self::LIST_TYPE_GROUP   => self::LIST_TYPE_GROUP,
        self::LIST_TYPE_IM      => 'ims',
    ];
    const EXPORT_PATH = 'export';
    const EXPORT_NAME = 'export';
    private $auth;
    private $zip;
    private $httpClient;
    private $file;
    private $baseUrl;
    private $url;
    /**
     * @var Parsedown
     */
    private $parsedown;

    public function __construct(
        HttpClient $httpClient,
        Zip $zip,
        File $file,
        Url $url,
        Parsedown $parsedown
    ) {
        $this->httpClient = $httpClient;
        $this->zip = $zip;
        $this->file = $file;
        $this->url = $url;
        $this->parsedown = $parsedown;
    }

    public function compressDownloadedMessages()
    {
        $destination = storage_path($this->auth->userId . '_' . self::EXPORT_NAME . '.zip');
        $this->zip->create($this->getExportPath(), $destination);

        return $destination;
    }

    protected function getExportPath($path = false)
    {
        return storage_path(self::EXPORT_PATH) . '/' . $this->auth->userId . '/' . ($path ? '/' . $path : '');
    }

    public function downloadGroupsMessages()
    {
        $this->saveMessages(
            self::LIST_TYPE_GROUP,
            $this->getList(self::LIST_TYPE_GROUP),
            function ($item) {
                return [
                    $item->name,
                    $item->fname,
                ];
            }
        );
    }

    private function saveMessages($type, $listItems, $nameAndBaseUrl)
    {
        $messages = [];
        foreach ($listItems as $item) {
            $groupMessages = $this->getMessages($type, $item->_id);
            list($name, $baseUrl) = $nameAndBaseUrl($item);

            foreach ($groupMessages as &$groupMessage) {
                $groupMessage = new Message($this->parsedown, $baseUrl, $groupMessage);
            }

            $messages[$item->_id] = view('message.index', [
                'name'      => $name,
                'messages'  => $groupMessages,
            ])->render();
            $this->downloadMessagesAttachments($type, $baseUrl, $groupMessages);
            $this->file->save($this->getExportPath($type . '/' . $baseUrl . '.html'),
                $messages[$item->_id]);
        }
    }

    public function getMessages($type, $id)
    {
        $messages = [];
        $count = true;
        $offset = 50;
        $inc = 0;
        while ($count) {
            $response = $this->request('GET', $type . '.messages?roomId=' . $id . '&offset=' . ($offset * $inc), [
                RequestOptions::HEADERS => $this->getAuthHeaders(),
            ]);
            if (!$response || 0 == $response->count) {
                $count = 0;
            } else {
                $count = $response->count;
                $messages = array_merge($messages, $response->messages);
            }
            $inc++;
        }

        return $messages;
    }

    public function request($type, $endpoint, $options = [])
    {
        $response = $this->httpClient->request(
            $type, $this->baseUrl . 'api/v1/' . $endpoint, $options
        );

        $responseBody = (string)$response->getBody();
        $data = json_decode($responseBody);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        return $responseBody;
    }

    protected function getAuthHeaders()
    {
        return [
            'X-Auth-Token' => $this->auth->authToken,
            'X-User-Id'    => $this->auth->userId,
        ];
    }

    public function downloadMessagesAttachments($type, $baseUrl, $messages)
    {
        foreach ($messages as $message) {
            if ($message->hasAttachment()) {
                foreach ($message->getAttachments() as $attachment) {
                    if ($attachment->hasTitleLink()) {
                        $response = $this->httpClient->request(
                            'GET', $this->baseUrl . $attachment->getTitleLink(), [
                                RequestOptions::HEADERS => $this->getAuthHeaders(),
                            ]
                        );

                        $this->file->save($this->getExportPath($type . '/' . $baseUrl . '/' . $attachment->getSafeTitleLink()),
                            $response->getBody()->getContents());
                    }
                }
            }
        }
    }

    public function getList($type)
    {
        $response = $this->request('GET', $type . '.list', [
            RequestOptions::FORM_PARAMS => [
                'offset' => 0,
                'count'  => 100,
            ],
            RequestOptions::HEADERS     => $this->getAuthHeaders(),
        ]);

        return $response->{self::LIST_TYPE_RESPONSE[$type]};
    }

    public function downloadImsMessages()
    {
        $this->saveMessages(
            self::LIST_TYPE_IM,
            $this->getList(self::LIST_TYPE_IM),
            function ($item) {
                return $this->getImUser($item->_id);
            }
        );
    }

    protected function getImUser($roomId)
    {
        $response = $this->request('GET', 'im.members?roomId=' . $roomId, [
            RequestOptions::HEADERS => $this->getAuthHeaders(),
        ]);

        foreach ($response->members as $member) {
            if ($member->_id != $this->auth->userId) {
                return [$member->name, $member->username];
            }
        }

        return ['anonymous', 'anonymous'];
    }

    public function downloadChannelsMessages()
    {
        $this->saveMessages(
            self::LIST_TYPE_CHANNEL,
            $this->getList(self::LIST_TYPE_CHANNEL),
            function ($item) {
                return [
                    $item->name,
                    $item->name,
                ];
            }
        );
    }

    public function deleteDownloadedMessages()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->getExportPath(), RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $todo = ($file->isDir() ? 'rmdir' : 'unlink');
            $todo($file->getRealPath());
        }
    }

    public function connect($url, $username, $password)
    {
        $this->baseUrl = $this->url->safe($url);
        $this->loginAs($username, $password);
    }

    protected function loginAs($username, $password)
    {
        try {
            $response = $this->request(
                'POST', 'login', [
                RequestOptions::FORM_PARAMS => [
                    'username' => $username,
                    'password' => $password,
                ],
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        if (($response->status ?? 'error') === 'success') {
            $this->auth = $response->data;

            return true;
        } else {
            $this->auth = null;
            throw new LoginErrorException('Invalid username or password');
        }
    }
}
