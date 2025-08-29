<?php

namespace MaxMessenger\Exceptions;

/**
 * Основное исключение для библиотеки MAX Messenger
 */
class MaxMessengerException extends \Exception
{
    /**
     * HTTP код ответа
     */
    protected ?int $httpCode = null;

    /**
     * Код ошибки API
     */
    protected ?string $apiErrorCode = null;

    /**
     * Дополнительные данные об ошибке
     */
    protected array $errorData = [];

    /**
     * Конструктор
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, ?int $httpCode = null, ?string $apiErrorCode = null, array $errorData = [])
    {
        parent::__construct($message, $code, $previous);
        $this->httpCode = $httpCode;
        $this->apiErrorCode = $apiErrorCode;
        $this->errorData = $errorData;
    }

    /**
     * Получить HTTP код ответа
     */
    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    /**
     * Получить код ошибки API
     */
    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    /**
     * Получить дополнительные данные об ошибке
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }

    /**
     * Создать исключение из ответа API
     */
    public static function fromApiResponse(array $response, ?int $httpCode = null): self
    {
        $error = $response['error'] ?? [];
        $message = $error['message'] ?? 'Unknown API error';
        $apiErrorCode = $error['code'] ?? null;
        $errorData = $error;

        return new self($message, 0, null, $httpCode, $apiErrorCode, $errorData);
    }

    /**
     * Создать исключение для ошибки аутентификации
     */
    public static function authenticationFailed(string $message = 'Authentication failed'): self
    {
        return new self($message, 401, null, 401, 'AUTHENTICATION_FAILED');
    }

    /**
     * Создать исключение для ошибки авторизации
     */
    public static function authorizationFailed(string $message = 'Authorization failed'): self
    {
        return new self($message, 403, null, 403, 'AUTHORIZATION_FAILED');
    }

    /**
     * Создать исключение для ошибки валидации
     */
    public static function validationFailed(string $message = 'Validation failed', array $errors = []): self
    {
        return new self($message, 400, null, 400, 'VALIDATION_FAILED', $errors);
    }

    /**
     * Создать исключение для ошибки "не найдено"
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self($message, 404, null, 404, 'NOT_FOUND');
    }

    /**
     * Создать исключение для ошибки "слишком много запросов"
     */
    public static function rateLimitExceeded(string $message = 'Rate limit exceeded'): self
    {
        return new self($message, 429, null, 429, 'RATE_LIMIT_EXCEEDED');
    }

    /**
     * Создать исключение для ошибки сервера
     */
    public static function serverError(string $message = 'Server error'): self
    {
        return new self($message, 500, null, 500, 'SERVER_ERROR');
    }

    /**
     * Создать исключение для ошибки "сервис недоступен"
     */
    public static function serviceUnavailable(string $message = 'Service unavailable'): self
    {
        return new self($message, 503, null, 503, 'SERVICE_UNAVAILABLE');
    }
}

