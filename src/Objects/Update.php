<?php

namespace MaxMessenger\Objects;

/**
 * Класс для представления обновлений
 * 
 * @see https://dev.max.ru/docs-api#update
 */
class Update
{
    public int $update_id;
    public ?Message $message;
    public ?Message $edited_message;
    public ?Message $channel_post;
    public ?Message $edited_channel_post;
    public ?array $callback_query;
    public ?array $chat_member;
    public ?array $chat_join_request;

    public function __construct(array $data)
    {
        $this->update_id = $data['update_id'] ?? 0;
        $this->message = isset($data['message']) ? new Message($data['message']) : null;
        $this->edited_message = isset($data['edited_message']) ? new Message($data['edited_message']) : null;
        $this->channel_post = isset($data['channel_post']) ? new Message($data['channel_post']) : null;
        $this->edited_channel_post = isset($data['edited_channel_post']) ? new Message($data['edited_channel_post']) : null;
        $this->callback_query = $data['callback_query'] ?? null;
        $this->chat_member = $data['chat_member'] ?? null;
        $this->chat_join_request = $data['chat_join_request'] ?? null;
    }

    /**
     * Получить ID обновления
     */
    public function getId(): int
    {
        return $this->update_id;
    }

    /**
     * Получить сообщение
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * Получить отредактированное сообщение
     */
    public function getEditedMessage(): ?Message
    {
        return $this->edited_message;
    }

    /**
     * Получить пост в канале
     */
    public function getChannelPost(): ?Message
    {
        return $this->channel_post;
    }

    /**
     * Получить отредактированный пост в канале
     */
    public function getEditedChannelPost(): ?Message
    {
        return $this->edited_channel_post;
    }

    /**
     * Получить callback запрос
     */
    public function getCallbackQuery(): ?array
    {
        return $this->callback_query;
    }

    /**
     * Получить информацию об участнике чата
     */
    public function getChatMember(): ?array
    {
        return $this->chat_member;
    }

    /**
     * Получить запрос на вступление в чат
     */
    public function getChatJoinRequest(): ?array
    {
        return $this->chat_join_request;
    }

    /**
     * Проверить, является ли обновление сообщением
     */
    public function isMessage(): bool
    {
        return $this->message !== null;
    }

    /**
     * Проверить, является ли обновление отредактированным сообщением
     */
    public function isEditedMessage(): bool
    {
        return $this->edited_message !== null;
    }

    /**
     * Проверить, является ли обновление постом в канале
     */
    public function isChannelPost(): bool
    {
        return $this->channel_post !== null;
    }

    /**
     * Проверить, является ли обновление отредактированным постом в канале
     */
    public function isEditedChannelPost(): bool
    {
        return $this->edited_channel_post !== null;
    }

    /**
     * Проверить, является ли обновление callback запросом
     */
    public function isCallbackQuery(): bool
    {
        return $this->callback_query !== null;
    }

    /**
     * Проверить, является ли обновление изменением участника чата
     */
    public function isChatMember(): bool
    {
        return $this->chat_member !== null;
    }

    /**
     * Проверить, является ли обновление запросом на вступление в чат
     */
    public function isChatJoinRequest(): bool
    {
        return $this->chat_join_request !== null;
    }

    /**
     * Получить тип обновления
     */
    public function getType(): string
    {
        if ($this->isMessage()) {
            return 'message';
        } elseif ($this->isEditedMessage()) {
            return 'edited_message';
        } elseif ($this->isChannelPost()) {
            return 'channel_post';
        } elseif ($this->isEditedChannelPost()) {
            return 'edited_channel_post';
        } elseif ($this->isCallbackQuery()) {
            return 'callback_query';
        } elseif ($this->isChatMember()) {
            return 'chat_member';
        } elseif ($this->isChatJoinRequest()) {
            return 'chat_join_request';
        }
        
        return 'unknown';
    }

    /**
     * Получить основное сообщение (любое из доступных)
     */
    public function getMainMessage(): ?Message
    {
        return $this->message ?? $this->edited_message ?? $this->channel_post ?? $this->edited_channel_post;
    }

    /**
     * Получить ID чата из основного сообщения
     */
    public function getChatId(): ?string
    {
        $message = $this->getMainMessage();
        return $message ? $message->getChat()?->getId() : null;
    }

    /**
     * Получить ID пользователя из основного сообщения
     */
    public function getUserId(): ?int
    {
        $message = $this->getMainMessage();
        return $message ? $message->getFrom()?->getId() : null;
    }

    /**
     * Получить текст из основного сообщения
     */
    public function getText(): ?string
    {
        $message = $this->getMainMessage();
        return $message ? $message->getText() : null;
    }

    /**
     * Получить данные callback запроса
     */
    public function getCallbackData(): ?string
    {
        if (!$this->isCallbackQuery()) {
            return null;
        }
        
        return $this->callback_query['data'] ?? null;
    }

    /**
     * Получить ID callback запроса
     */
    public function getCallbackId(): ?string
    {
        if (!$this->isCallbackQuery()) {
            return null;
        }
        
        return $this->callback_query['id'] ?? null;
    }

    /**
     * Получить пользователя из callback запроса
     */
    public function getCallbackUser(): ?User
    {
        if (!$this->isCallbackQuery() || !isset($this->callback_query['from'])) {
            return null;
        }
        
        return new User($this->callback_query['from']);
    }

    /**
     * Получить сообщение из callback запроса
     */
    public function getCallbackMessage(): ?Message
    {
        if (!$this->isCallbackQuery() || !isset($this->callback_query['message'])) {
            return null;
        }
        
        return new Message($this->callback_query['message']);
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'update_id' => $this->update_id,
            'message' => $this->message ? $this->message->toArray() : null,
            'edited_message' => $this->edited_message ? $this->edited_message->toArray() : null,
            'channel_post' => $this->channel_post ? $this->channel_post->toArray() : null,
            'edited_channel_post' => $this->edited_channel_post ? $this->edited_channel_post->toArray() : null,
            'callback_query' => $this->callback_query,
            'chat_member' => $this->chat_member,
            'chat_join_request' => $this->chat_join_request,
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
