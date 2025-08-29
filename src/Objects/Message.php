<?php

namespace MaxMessenger\Objects;

/**
 * Класс для представления сообщения
 * 
 * @see https://dev.max.ru/docs-api#message
 */
class Message
{
    public int $message_id;
    public ?User $from;
    public ?Chat $chat;
    public int $date;
    public ?string $text;
    public array $attachments;
    public ?Message $reply_to_message;
    public ?int $edit_date;
    public ?string $author_signature;
    public ?string $forward_signature;
    public ?int $forward_date;
    public ?bool $is_automatic_forward;
    public ?User $via_bot;
    public ?string $media_group_id;
    public array $entities;

    public function __construct(array $data)
    {
        $this->message_id = $data['message_id'] ?? 0;
        $this->from = isset($data['from']) ? new User($data['from']) : null;
        $this->chat = isset($data['chat']) ? new Chat($data['chat']) : null;
        $this->date = $data['date'] ?? 0;
        $this->text = $data['text'] ?? null;
        $this->attachments = $data['attachments'] ?? [];
        $this->reply_to_message = isset($data['reply_to_message']) ? new Message($data['reply_to_message']) : null;
        $this->edit_date = $data['edit_date'] ?? null;
        $this->author_signature = $data['author_signature'] ?? null;
        $this->forward_signature = $data['forward_signature'] ?? null;
        $this->forward_date = $data['forward_date'] ?? null;
        $this->is_automatic_forward = $data['is_automatic_forward'] ?? null;
        $this->via_bot = isset($data['via_bot']) ? new User($data['via_bot']) : null;
        $this->media_group_id = $data['media_group_id'] ?? null;
        $this->entities = $data['entities'] ?? [];
    }

    /**
     * Получить ID сообщения
     */
    public function getId(): int
    {
        return $this->message_id;
    }

    /**
     * Получить отправителя
     */
    public function getFrom(): ?User
    {
        return $this->from;
    }

    /**
     * Получить чат
     */
    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    /**
     * Получить дату отправки
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * Получить дату отправки в формате DateTime
     */
    public function getDateTime(): \DateTime
    {
        return new \DateTime('@' . $this->date);
    }

    /**
     * Получить текст сообщения
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Получить вложения
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Получить сообщение, на которое отвечает данное
     */
    public function getReplyToMessage(): ?Message
    {
        return $this->reply_to_message;
    }

    /**
     * Получить дату редактирования
     */
    public function getEditDate(): ?int
    {
        return $this->edit_date;
    }

    /**
     * Получить подпись автора
     */
    public function getAuthorSignature(): ?string
    {
        return $this->author_signature;
    }

    /**
     * Получить подпись пересылки
     */
    public function getForwardSignature(): ?string
    {
        return $this->forward_signature;
    }

    /**
     * Получить дату пересылки
     */
    public function getForwardDate(): ?int
    {
        return $this->forward_date;
    }

    /**
     * Проверить, является ли пересылка автоматической
     */
    public function isAutomaticForward(): ?bool
    {
        return $this->is_automatic_forward;
    }

    /**
     * Получить бота, через которого отправлено сообщение
     */
    public function getViaBot(): ?User
    {
        return $this->via_bot;
    }

    /**
     * Получить ID медиа-группы
     */
    public function getMediaGroupId(): ?string
    {
        return $this->media_group_id;
    }

    /**
     * Получить сущности сообщения
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * Проверить, является ли сообщение текстовым
     */
    public function isText(): bool
    {
        return $this->text !== null && $this->text !== '';
    }

    /**
     * Проверить, является ли сообщение ответом на другое
     */
    public function isReply(): bool
    {
        return $this->reply_to_message !== null;
    }

    /**
     * Проверить, было ли сообщение отредактировано
     */
    public function isEdited(): bool
    {
        return $this->edit_date !== null;
    }

    /**
     * Проверить, является ли сообщение пересылкой
     */
    public function isForward(): bool
    {
        return $this->forward_date !== null;
    }

    /**
     * Проверить, есть ли у сообщения вложения
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * Проверить, есть ли у сообщения медиа-группа
     */
    public function hasMediaGroup(): bool
    {
        return $this->media_group_id !== null;
    }

    /**
     * Получить количество вложений
     */
    public function getAttachmentsCount(): int
    {
        return count($this->attachments);
    }

    /**
     * Получить вложение по типу
     */
    public function getAttachmentByType(string $type): ?array
    {
        foreach ($this->attachments as $attachment) {
            if (($attachment['type'] ?? '') === $type) {
                return $attachment;
            }
        }
        return null;
    }

    /**
     * Проверить, есть ли вложение определенного типа
     */
    public function hasAttachmentType(string $type): bool
    {
        return $this->getAttachmentByType($type) !== null;
    }

    /**
     * Получить все вложения определенного типа
     */
    public function getAttachmentsByType(string $type): array
    {
        $result = [];
        foreach ($this->attachments as $attachment) {
            if (($attachment['type'] ?? '') === $type) {
                $result[] = $attachment;
            }
        }
        return $result;
    }

    /**
     * Получить время с момента отправки в секундах
     */
    public function getAge(): int
    {
        return time() - $this->date;
    }

    /**
     * Проверить, является ли сообщение старым (старше N секунд)
     */
    public function isOld(int $seconds = 3600): bool
    {
        return $this->getAge() > $seconds;
    }

    /**
     * Получить форматированный возраст сообщения
     */
    public function getFormattedAge(): string
    {
        $age = $this->getAge();
        
        if ($age < 60) {
            return $age . ' сек назад';
        } elseif ($age < 3600) {
            return floor($age / 60) . ' мин назад';
        } elseif ($age < 86400) {
            return floor($age / 3600) . ' ч назад';
        } else {
            return floor($age / 86400) . ' дн назад';
        }
    }

    /**
     * Преобразовать в массив
     */
    public function toArray(): array
    {
        return [
            'message_id' => $this->message_id,
            'from' => $this->from ? $this->from->toArray() : null,
            'chat' => $this->chat ? $this->chat->toArray() : null,
            'date' => $this->date,
            'text' => $this->text,
            'attachments' => $this->attachments,
            'reply_to_message' => $this->reply_to_message ? $this->reply_to_message->toArray() : null,
            'edit_date' => $this->edit_date,
            'author_signature' => $this->author_signature,
            'forward_signature' => $this->forward_signature,
            'forward_date' => $this->forward_date,
            'is_automatic_forward' => $this->is_automatic_forward,
            'via_bot' => $this->via_bot ? $this->via_bot->toArray() : null,
            'media_group_id' => $this->media_group_id,
            'entities' => $this->entities,
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
