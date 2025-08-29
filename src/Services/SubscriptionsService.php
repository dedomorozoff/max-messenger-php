<?php

namespace MaxMessenger\Services;

/**
 * Сервис для работы с подписками
 * 
 * @see https://dev.max.ru/docs-api#subscriptions
 */
class SubscriptionsService extends AbstractService
{
    /**
     * Получить подписки
     * 
     * @return array Список подписок
     */
    public function getSubscriptions(): array
    {
        $response = $this->get('/subscriptions');
        return $this->checkResponse($response);
    }

    /**
     * Подписаться на обновления
     * 
     * @param string $webhookUrl URL webhook'а
     * @param array $options Дополнительные опции
     * @return array Результат подписки
     */
    public function subscribe(string $webhookUrl, array $options = []): array
    {
        $data = array_merge([
            'url' => $webhookUrl,
        ], $options);

        $response = $this->post('/subscriptions', $data);
        return $this->checkResponse($response);
    }

    /**
     * Отписаться от обновлений
     * 
     * @return array Результат отписки
     */
    public function unsubscribe(): array
    {
        $response = $this->delete('/subscriptions');
        return $this->checkResponse($response);
    }

    /**
     * Получить обновления
     * 
     * @param array $params Параметры запроса
     * @return array Список обновлений
     */
    public function getUpdates(array $params = []): array
    {
        $response = $this->get('/subscriptions/updates', $params);
        return $this->checkResponse($response);
    }

    /**
     * Получить последние обновления
     * 
     * @param int $limit Количество обновлений
     * @param int $offset Смещение
     * @return array Список обновлений
     */
    public function getRecentUpdates(int $limit = 100, int $offset = 0): array
    {
        return $this->getUpdates([
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Получить обновления с определенного времени
     * 
     * @param int $timestamp Временная метка
     * @param int $limit Количество обновлений
     * @return array Список обновлений
     */
    public function getUpdatesSince(int $timestamp, int $limit = 100): array
    {
        return $this->getUpdates([
            'since' => $timestamp,
            'limit' => $limit
        ]);
    }

    /**
     * Проверить, есть ли активные подписки
     * 
     * @return bool True если есть активные подписки
     */
    public function hasActiveSubscriptions(): bool
    {
        $subscriptions = $this->getSubscriptions();
        return !empty($subscriptions['subscriptions'] ?? []);
    }

    /**
     * Получить количество активных подписок
     * 
     * @return int Количество подписок
     */
    public function getSubscriptionsCount(): int
    {
        $subscriptions = $this->getSubscriptions();
        return count($subscriptions['subscriptions'] ?? []);
    }

    /**
     * Получить webhook URL активной подписки
     * 
     * @return string|null URL webhook'а или null
     */
    public function getActiveWebhookUrl(): ?string
    {
        $subscriptions = $this->getSubscriptions();
        $activeSubscriptions = $subscriptions['subscriptions'] ?? [];
        
        if (!empty($activeSubscriptions)) {
            return $activeSubscriptions[0]['url'] ?? null;
        }
        
        return null;
    }

    /**
     * Обновить webhook URL
     * 
     * @param string $newWebhookUrl Новый URL webhook'а
     * @return array Результат обновления
     */
    public function updateWebhookUrl(string $newWebhookUrl): array
    {
        // Сначала отписываемся от старых
        $this->unsubscribe();
        
        // Затем подписываемся на новые
        return $this->subscribe($newWebhookUrl);
    }

    /**
     * Получить информацию о webhook подписке
     * 
     * @return array|null Информация о подписке или null
     */
    public function getWebhookInfo(): ?array
    {
        $subscriptions = $this->getSubscriptions();
        $activeSubscriptions = $subscriptions['subscriptions'] ?? [];
        
        if (!empty($activeSubscriptions)) {
            return $activeSubscriptions[0] ?? null;
        }
        
        return null;
    }

    /**
     * Проверить статус webhook'а
     * 
     * @return bool True если webhook активен
     */
    public function isWebhookActive(): bool
    {
        $webhookInfo = $this->getWebhookInfo();
        return $webhookInfo !== null && ($webhookInfo['status'] ?? '') === 'active';
    }

    /**
     * Получить время последнего обновления
     * 
     * @return int|null Timestamp последнего обновления или null
     */
    public function getLastUpdateTime(): ?int
    {
        $updates = $this->getRecentUpdates(1);
        $lastUpdate = $updates['updates'][0] ?? null;
        
        if ($lastUpdate) {
            return $lastUpdate['update_id'] ?? null;
        }
        
        return null;
    }

    /**
     * Получить количество непрочитанных обновлений
     * 
     * @return int Количество непрочитанных обновлений
     */
    public function getUnreadUpdatesCount(): int
    {
        $updates = $this->getUpdates(['unread_only' => true]);
        return count($updates['updates'] ?? []);
    }

    /**
     * Отметить обновления как прочитанные
     * 
     * @param array $updateIds Массив ID обновлений
     * @return array Результат отметки
     */
    public function markUpdatesAsRead(array $updateIds): array
    {
        $response = $this->post('/subscriptions/updates/read', [
            'update_ids' => $updateIds
        ]);
        return $this->checkResponse($response);
    }
}

