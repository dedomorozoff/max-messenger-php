<?php

namespace MaxMessenger\Services;

/**
 * Сервис для работы с сообщениями
 * 
 * @see https://dev.max.ru/docs-api#messages
 */
class MessagesService extends AbstractService
{
    /**
     * Получить сообщения
     * 
     * @param array $params Параметры запроса
     * @return array Список сообщений
     */
    public function get(array $params = []): array
    {
        $response = $this->get('/messages', $params);
        return $this->checkResponse($response);
    }

    /**
     * Отправить сообщение
     * 
     * @param array $data Данные сообщения
     * @return array Результат отправки
     */
    public function send(array $data): array
    {
        $response = $this->post('/messages', $data);
        return $this->checkResponse($response);
    }

    /**
     * Редактировать сообщение
     * 
     * @param string $messageId ID сообщения
     * @param array $data Новые данные сообщения
     * @return array Результат редактирования
     */
    public function edit(string $messageId, array $data): array
    {
        $response = $this->put("/messages/{$messageId}", $data);
        return $this->checkResponse($response);
    }

    /**
     * Удалить сообщение
     * 
     * @param string $messageId ID сообщения
     * @return array Результат удаления
     */
    public function delete(string $messageId): array
    {
        $response = $this->delete("/messages/{$messageId}");
        return $this->checkResponse($response);
    }

    /**
     * Получить конкретное сообщение
     * 
     * @param string $messageId ID сообщения
     * @return array Информация о сообщении
     */
    public function getById(string $messageId): array
    {
        $response = $this->get("/messages/{$messageId}");
        return $this->checkResponse($response);
    }

    /**
     * Получить информацию о видео
     * 
     * @param string $messageId ID сообщения
     * @return array Информация о видео
     */
    public function getVideoInfo(string $messageId): array
    {
        $response = $this->get("/messages/{$messageId}/video");
        return $this->checkResponse($response);
    }

    /**
     * Ответить на callback
     * 
     * @param string $callbackId ID callback
     * @param string $text Текст ответа
     * @return array Результат ответа
     */
    public function answerCallback(string $callbackId, string $text): array
    {
        $response = $this->post("/messages/{$callbackId}/callback", [
            'text' => $text
        ]);
        return $this->checkResponse($response);
    }

    /**
     * Отправить текстовое сообщение
     * 
     * @param string $chatId ID чата
     * @param string $text Текст сообщения
     * @param array $options Дополнительные опции
     * @return array Результат отправки
     */
    public function sendText(string $chatId, string $text, array $options = []): array
    {
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options);

        return $this->send($data);
    }

    /**
     * Отправить сообщение с Markdown форматированием
     * 
     * @param string $chatId ID чата
     * @param string $text Текст с Markdown разметкой
     * @param array $options Дополнительные опции
     * @return array Результат отправки
     */
    public function sendMarkdown(string $chatId, string $text, array $options = []): array
    {
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'format' => 'markdown',
        ], $options);

        return $this->send($data);
    }

    /**
     * Отправить сообщение с HTML форматированием
     * 
     * @param string $chatId ID чата
     * @param string $text Текст с HTML разметкой
     * @param array $options Дополнительные опции
     * @return array Результат отправки
     */
    public function sendHtml(string $chatId, string $text, array $options = []): array
    {
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'format' => 'html',
        ], $options);

        return $this->send($data);
    }

    /**
     * Отправить сообщение с inline клавиатурой
     * 
     * @param string $chatId ID чата
     * @param string $text Текст сообщения
     * @param array $buttons Массив кнопок
     * @param array $options Дополнительные опции
     * @return array Результат отправки
     */
    public function sendWithKeyboard(string $chatId, string $text, array $buttons, array $options = []): array
    {
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'attachments' => [
                [
                    'type' => 'inline_keyboard',
                    'payload' => [
                        'buttons' => $buttons
                    ]
                ]
            ]
        ], $options);

        return $this->send($data);
    }

    /**
     * Создать callback кнопку
     * 
     * @param string $text Текст кнопки
     * @param string $payload Данные кнопки
     * @return array Массив кнопки
     */
    public function createCallbackButton(string $text, string $payload): array
    {
        return [
            'type' => 'callback',
            'text' => $text,
            'payload' => $payload
        ];
    }

    /**
     * Создать ссылку кнопку
     * 
     * @param string $text Текст кнопки
     * @param string $url URL ссылки
     * @return array Массив кнопки
     */
    public function createLinkButton(string $text, string $url): array
    {
        return [
            'type' => 'link',
            'text' => $text,
            'url' => $url
        ];
    }

    /**
     * Создать кнопку запроса контакта
     * 
     * @param string $text Текст кнопки
     * @return array Массив кнопки
     */
    public function createContactButton(string $text): array
    {
        return [
            'type' => 'request_contact',
            'text' => $text
        ];
    }

    /**
     * Создать кнопку запроса местоположения
     * 
     * @param string $text Текст кнопки
     * @return array Массив кнопки
     */
    public function createLocationButton(string $text): array
    {
        return [
            'type' => 'request_geo_location',
            'text' => $text
        ];
    }

    /**
     * Создать кнопку открытия приложения
     * 
     * @param string $text Текст кнопки
     * @param string $appId ID приложения
     * @return array Массив кнопки
     */
    public function createAppButton(string $text, string $appId): array
    {
        return [
            'type' => 'open_app',
            'text' => $text,
            'app_id' => $appId
        ];
    }

    /**
     * Создать кнопку отправки сообщения
     * 
     * @param string $text Текст кнопки
     * @param string $message Текст сообщения для отправки
     * @return array Массив кнопки
     */
    public function createMessageButton(string $text, string $message): array
    {
        return [
            'type' => 'message',
            'text' => $text,
            'message' => $message
        ];
    }
}

