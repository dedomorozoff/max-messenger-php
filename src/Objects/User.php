<?php

namespace MaxMessenger\Objects;

/**
 * Класс для представления пользователя
 * 
 * @see https://dev.max.ru/docs-api#user
 */
class User
{
    public int $user_id;
    public string $name;
    public string $username;
    public bool $is_bot;
    public int $last_activity_time;

    public function __construct(array $data)
    {
        $this->user_id = $data['user_id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->is_bot = $data['is_bot'] ?? false;
        $this->last_activity_time = $data['last_activity_time'] ?? 0;
    }

    /**
     * Получить ID пользователя
     */
    public function getId(): int
    {
        return $this->user_id;
    }

    /**
     * Получить имя пользователя
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Получить username пользователя
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Проверить, является ли пользователь ботом
     */
    public function isBot(): bool
    {
        return $this->is_bot;
    }

    /**
     * Получить время последней активности
     */
    public function getLastActivityTime(): int
    {
        return $this->last_activity_time;
    }

    /**
     * Получить время последней активности в формате DateTime
     */
    public function getLastActivityDateTime(): \DateTime
    {
        return new \DateTime('@' . ($this->last_activity_time / 1000));
    }

    /**
     * Проверить, активен ли пользователь в последние N минут
     */
    public function isActive(int $minutes = 5): bool
    {
        $now = time() * 1000;
        $threshold = $minutes * 60 * 1000;
        return ($now - $this->last_activity_time) < $threshold;
    }

    /**
     * Получить ссылку на пользователя
     */
    public function getLink(): string
    {
        return "max://max.ru/{$this->username}";
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'username' => $this->username,
            'is_bot' => $this->is_bot,
            'last_activity_time' => $this->last_activity_time,
        ];
    }

    /**
     * Создать из массива
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
