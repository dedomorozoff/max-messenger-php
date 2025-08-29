<?php

namespace MaxMessenger;

use MaxMessenger\Services\BotService;
use MaxMessenger\Services\ChatsService;
use MaxMessenger\Services\MessagesService;
use MaxMessenger\Services\SubscriptionsService;
use MaxMessenger\Services\UploadService;
use MaxMessenger\Exceptions\MaxMessengerException;

class MaxMessenger
{
    private array $config;
    private \GuzzleHttp\Client $httpClient;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'bot_token' => null,
            'base_url' => 'https://botapi.max.ru',
            'timeout' => 30,
            'verify' => true,
        ], $config);

        if (empty($this->config['bot_token'])) {
            throw new MaxMessengerException('Bot token is required');
        }

        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => $this->config['timeout'],
            'verify' => $this->config['verify'],
            'headers' => [
                'User-Agent' => 'MaxMessenger-PHP/1.0',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Получить сервис для работы с ботом
     */
    public function bot(): BotService
    {
        return new BotService($this->httpClient, $this->config);
    }

    /**
     * Получить сервис для работы с чатами
     */
    public function chats(): ChatsService
    {
        return new ChatsService($this->httpClient, $this->config);
    }

    /**
     * Получить сервис для работы с сообщениями
     */
    public function messages(): MessagesService
    {
        return new MessagesService($this->httpClient, $this->config);
    }

    /**
     * Получить сервис для работы с подписками
     */
    public function subscriptions(): SubscriptionsService
    {
        return new SubscriptionsService($this->httpClient, $this->config);
    }

    /**
     * Получить сервис для загрузки файлов
     */
    public function upload(): UploadService
    {
        return new UploadService($this->httpClient, $this->config);
    }

    /**
     * Получить конфигурацию
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Получить HTTP клиент
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->httpClient;
    }
}

