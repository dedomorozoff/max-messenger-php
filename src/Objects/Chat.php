<?php

namespace MaxMessenger\Objects;

/**
 * Класс для представления чата
 * 
 * @see https://dev.max.ru/docs-api#chat
 */
class Chat
{
    public string $chat_id;
    public string $type;
    public string $title;
    public ?string $description;
    public ?string $photo;
    public array $permissions;
    public ?int $slow_mode_delay;
    public ?string $invite_link;
    public ?int $pinned_message_id;
    public ?int $sticker_set_name;
    public ?bool $can_set_sticker_set;

    public function __construct(array $data)
    {
        $this->chat_id = $data['chat_id'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->photo = $data['photo'] ?? null;
        $this->permissions = $data['permissions'] ?? [];
        $this->slow_mode_delay = $data['slow_mode_delay'] ?? null;
        $this->invite_link = $data['invite_link'] ?? null;
        $this->pinned_message_id = $data['pinned_message_id'] ?? null;
        $this->sticker_set_name = $data['sticker_set_name'] ?? null;
        $this->can_set_sticker_set = $data['can_set_sticker_set'] ?? null;
    }

    /**
     * Получить ID чата
     */
    public function getId(): string
    {
        return $this->chat_id;
    }

    /**
     * Получить тип чата
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Получить название чата
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Получить описание чата
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Получить фото чата
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * Получить разрешения чата
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Получить задержку медленного режима
     */
    public function getSlowModeDelay(): ?int
    {
        return $this->slow_mode_delay;
    }

    /**
     * Получить ссылку-приглашение
     */
    public function getInviteLink(): ?string
    {
        return $this->invite_link;
    }

    /**
     * Получить ID закрепленного сообщения
     */
    public function getPinnedMessageId(): ?int
    {
        return $this->pinned_message_id;
    }

    /**
     * Получить название набора стикеров
     */
    public function getStickerSetName(): ?int
    {
        return $this->sticker_set_name;
    }

    /**
     * Проверить, можно ли установить набор стикеров
     */
    public function canSetStickerSet(): ?bool
    {
        return $this->can_set_sticker_set;
    }

    /**
     * Проверить, является ли чат приватным
     */
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Проверить, является ли чат групповым
     */
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Проверить, является ли чат каналом
     */
    public function isChannel(): bool
    {
        return $this->type === 'channel';
    }

    /**
     * Проверить, является ли чат супергруппой
     */
    public function isSupergroup(): bool
    {
        return $this->type === 'supergroup';
    }

    /**
     * Проверить, есть ли у чата фото
     */
    public function hasPhoto(): bool
    {
        return $this->photo !== null;
    }

    /**
     * Проверить, есть ли у чата описание
     */
    public function hasDescription(): bool
    {
        return $this->description !== null && $this->description !== '';
    }

    /**
     * Проверить, есть ли у чата закрепленное сообщение
     */
    public function hasPinnedMessage(): bool
    {
        return $this->pinned_message_id !== null;
    }

    /**
     * Проверить, есть ли у чата ссылка-приглашение
     */
    public function hasInviteLink(): bool
    {
        return $this->invite_link !== null;
    }

    /**
     * Проверить, включен ли медленный режим
     */
    public function hasSlowMode(): bool
    {
        return $this->slow_mode_delay !== null && $this->slow_mode_delay > 0;
    }

    /**
     * Получить разрешение по ключу
     */
    public function getPermission(string $key): bool
    {
        return $this->permissions[$key] ?? false;
    }

    /**
     * Проверить, может ли пользователь отправлять сообщения
     */
    public function canSendMessages(): bool
    {
        return $this->getPermission('can_send_messages');
    }

    /**
     * Проверить, может ли пользователь отправлять медиа
     */
    public function canSendMediaMessages(): bool
    {
        return $this->getPermission('can_send_media_messages');
    }

    /**
     * Проверить, может ли пользователь отправлять опросы
     */
    public function canSendPolls(): bool
    {
        return $this->getPermission('can_send_polls');
    }

    /**
     * Проверить, может ли пользователь отправлять другие типы сообщений
     */
    public function canSendOtherMessages(): bool
    {
        return $this->getPermission('can_send_other_messages');
    }

    /**
     * Проверить, может ли пользователь добавлять веб-превью
     */
    public function canAddWebPagePreviews(): bool
    {
        return $this->getPermission('can_add_web_page_previews');
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'chat_id' => $this->chat_id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $this->photo,
            'permissions' => $this->permissions,
            'slow_mode_delay' => $this->slow_mode_delay,
            'invite_link' => $this->invite_link,
            'pinned_message_id' => $this->pinned_message_id,
            'sticker_set_name' => $this->sticker_set_name,
            'can_set_sticker_set' => $this->can_set_sticker_set,
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
