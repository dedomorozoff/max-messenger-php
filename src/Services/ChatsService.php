<?php

namespace MaxMessenger\Services;

/**
 * Сервис для работы с чатами
 * 
 * @see https://dev.max.ru/docs-api#chats
 */
class ChatsService extends AbstractService
{
    /**
     * Получить список всех чатов
     * 
     * @param array $params Параметры запроса
     * @return array Список чатов
     */
    public function getAll(array $params = []): array
    {
        $response = $this->get('/chats', $params);
        return $this->checkResponse($response);
    }

    /**
     * Получить чат по ссылке
     * 
     * @param string $link Ссылка на чат
     * @return array Информация о чате
     */
    public function getByLink(string $link): array
    {
        $response = $this->get('/chats/link', ['link' => $link]);
        return $this->checkResponse($response);
    }

    /**
     * Получить информацию о чате
     * 
     * @param string $chatId ID чата
     * @return array Информация о чате
     */
    public function getInfo(string $chatId): array
    {
        $response = $this->get("/chats/{$chatId}");
        return $this->checkResponse($response);
    }

    /**
     * Изменить информацию о чате
     * 
     * @param string $chatId ID чата
     * @param array $data Данные для обновления
     * @return array Результат обновления
     */
    public function update(string $chatId, array $data): array
    {
        $response = $this->patch("/chats/{$chatId}", $data);
        return $this->checkResponse($response);
    }

    /**
     * Удалить чат
     * 
     * @param string $chatId ID чата
     * @return array Результат удаления
     */
    public function delete(string $chatId): array
    {
        $response = $this->delete("/chats/{$chatId}");
        return $this->checkResponse($response);
    }

    /**
     * Отправить действие в чат
     * 
     * @param string $chatId ID чата
     * @param string $action Действие
     * @return array Результат действия
     */
    public function sendAction(string $chatId, string $action): array
    {
        $response = $this->post("/chats/{$chatId}/actions", ['action' => $action]);
        return $this->checkResponse($response);
    }

    /**
     * Получить закрепленное сообщение
     * 
     * @param string $chatId ID чата
     * @return array Закрепленное сообщение
     */
    public function getPinnedMessage(string $chatId): array
    {
        $response = $this->get("/chats/{$chatId}/pinned_message");
        return $this->checkResponse($response);
    }

    /**
     * Закрепить сообщение
     * 
     * @param string $chatId ID чата
     * @param string $messageId ID сообщения
     * @return array Результат закрепления
     */
    public function pinMessage(string $chatId, string $messageId): array
    {
        $response = $this->put("/chats/{$chatId}/pinned_message", ['message_id' => $messageId]);
        return $this->checkResponse($response);
    }

    /**
     * Удалить закрепленное сообщение
     * 
     * @param string $chatId ID чата
     * @return array Результат удаления
     */
    public function unpinMessage(string $chatId): array
    {
        $response = $this->delete("/chats/{$chatId}/pinned_message");
        return $this->checkResponse($response);
    }

    /**
     * Получить информацию о членстве в чате
     * 
     * @param string $chatId ID чата
     * @return array Информация о членстве
     */
    public function getMembership(string $chatId): array
    {
        $response = $this->get("/chats/{$chatId}/membership");
        return $this->checkResponse($response);
    }

    /**
     * Удалить бота из чата
     * 
     * @param string $chatId ID чата
     * @return array Результат удаления
     */
    public function leave(string $chatId): array
    {
        $response = $this->delete("/chats/{$chatId}/membership");
        return $this->checkResponse($response);
    }

    /**
     * Получить список администраторов чата
     * 
     * @param string $chatId ID чата
     * @return array Список администраторов
     */
    public function getAdmins(string $chatId): array
    {
        $response = $this->get("/chats/{$chatId}/admins");
        return $this->checkResponse($response);
    }

    /**
     * Назначить администратора чата
     * 
     * @param string $chatId ID чата
     * @param string $userId ID пользователя
     * @return array Результат назначения
     */
    public function addAdmin(string $chatId, string $userId): array
    {
        $response = $this->post("/chats/{$chatId}/admins", ['user_id' => $userId]);
        return $this->checkResponse($response);
    }

    /**
     * Отменить права администратора
     * 
     * @param string $chatId ID чата
     * @param string $userId ID пользователя
     * @return array Результат отмены
     */
    public function removeAdmin(string $chatId, string $userId): array
    {
        $response = $this->delete("/chats/{$chatId}/admins", ['user_id' => $userId]);
        return $this->checkResponse($response);
    }

    /**
     * Получить участников чата
     * 
     * @param string $chatId ID чата
     * @param array $params Параметры запроса
     * @return array Список участников
     */
    public function getMembers(string $chatId, array $params = []): array
    {
        $response = $this->get("/chats/{$chatId}/members", $params);
        return $this->checkResponse($response);
    }

    /**
     * Добавить участников в чат
     * 
     * @param string $chatId ID чата
     * @param array $userIds Массив ID пользователей
     * @return array Результат добавления
     */
    public function addMembers(string $chatId, array $userIds): array
    {
        $response = $this->post("/chats/{$chatId}/members", ['user_ids' => $userIds]);
        return $this->checkResponse($response);
    }

    /**
     * Удалить участника из чата
     * 
     * @param string $chatId ID чата
     * @param string $userId ID пользователя
     * @return array Результат удаления
     */
    public function removeMember(string $chatId, string $userId): array
    {
        $response = $this->delete("/chats/{$chatId}/members", ['user_id' => $userId]);
        return $this->checkResponse($response);
    }

    /**
     * Получить название чата
     * 
     * @param string $chatId ID чата
     * @return string Название чата
     */
    public function getTitle(string $chatId): string
    {
        $info = $this->getInfo($chatId);
        return $info['title'] ?? '';
    }

    /**
     * Изменить название чата
     * 
     * @param string $chatId ID чата
     * @param string $title Новое название
     * @return array Результат изменения
     */
    public function setTitle(string $chatId, string $title): array
    {
        return $this->update($chatId, ['title' => $title]);
    }

    /**
     * Получить тип чата
     * 
     * @param string $chatId ID чата
     * @return string Тип чата
     */
    public function getType(string $chatId): string
    {
        $info = $this->getInfo($chatId);
        return $info['type'] ?? '';
    }

    /**
     * Проверить, является ли чат приватным
     * 
     * @param string $chatId ID чата
     * @return bool True если чат приватный
     */
    public function isPrivate(string $chatId): bool
    {
        $info = $this->getInfo($chatId);
        return ($info['type'] ?? '') === 'private';
    }

    /**
     * Проверить, является ли чат групповым
     * 
     * @param string $chatId ID чата
     * @return bool True если чат групповой
     */
    public function isGroup(string $chatId): bool
    {
        $info = $this->getInfo($chatId);
        return ($info['type'] ?? '') === 'group';
    }

    /**
     * Проверить, является ли чат каналом
     * 
     * @param string $chatId ID чата
     * @return bool True если чат канал
     */
    public function isChannel(string $chatId): bool
    {
        $info = $this->getInfo($chatId);
        return ($info['type'] ?? '') === 'channel';
    }
}

