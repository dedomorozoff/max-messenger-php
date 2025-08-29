<?php

namespace MaxMessenger\Services;

/**
 * Сервис для работы с ботом
 * 
 * @see https://dev.max.ru/docs-api#bots
 */
class BotService extends AbstractService
{
    /**
     * Получить информацию о текущем боте
     * 
     * @return array Информация о боте
     */
    public function getInfo(): array
    {
        $response = $this->get('/me');
        return $this->checkResponse($response);
    }

    /**
     * Изменить информацию о текущем боте
     * 
     * @param array $data Данные для обновления
     * @return array Результат обновления
     */
    public function update(array $data): array
    {
        $response = $this->patch('/me', $data);
        return $this->checkResponse($response);
    }

    /**
     * Получить ID бота
     * 
     * @return int ID бота
     */
    public function getId(): int
    {
        $info = $this->getInfo();
        return $info['user_id'] ?? 0;
    }

    /**
     * Получить имя бота
     * 
     * @return string Имя бота
     */
    public function getName(): string
    {
        $info = $this->getInfo();
        return $info['name'] ?? '';
    }

    /**
     * Получить username бота
     * 
     * @return string Username бота
     */
    public function getUsername(): string
    {
        $info = $this->getInfo();
        return $info['username'] ?? '';
    }

    /**
     * Проверить, является ли пользователь ботом
     * 
     * @return bool True если это бот
     */
    public function isBot(): bool
    {
        $info = $this->getInfo();
        return $info['is_bot'] ?? false;
    }

    /**
     * Получить время последней активности
     * 
     * @return int Timestamp последней активности
     */
    public function getLastActivityTime(): int
    {
        $info = $this->getInfo();
        return $info['last_activity_time'] ?? 0;
    }
}

