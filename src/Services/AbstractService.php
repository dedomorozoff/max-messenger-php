<?php

namespace MaxMessenger\Services;

use MaxMessenger\Exceptions\MaxMessengerException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractService
{
    protected Client $httpClient;
    protected array $config;

    public function __construct(Client $httpClient, array $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Выполнить GET запрос
     */
    protected function get(string $endpoint, array $params = []): array
    {
        $params['access_token'] = $this->config['bot_token'];
        
        try {
            $response = $this->httpClient->get($endpoint, [
                'query' => $params
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new MaxMessengerException('GET request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Выполнить POST запрос
     */
    protected function post(string $endpoint, array $data = [], array $params = []): array
    {
        $params['access_token'] = $this->config['bot_token'];
        
        try {
            $response = $this->httpClient->post($endpoint, [
                'query' => $params,
                'json' => $data
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new MaxMessengerException('POST request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Выполнить PUT запрос
     */
    protected function put(string $endpoint, array $data = [], array $params = []): array
    {
        $params['access_token'] = $this->config['bot_token'];
        
        try {
            $response = $this->httpClient->put($endpoint, [
                'query' => $params,
                'json' => $data
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new MaxMessengerException('PUT request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Выполнить PATCH запрос
     */
    protected function patch(string $endpoint, array $data = [], array $params = []): array
    {
        $params['access_token'] = $this->config['bot_token'];
        
        try {
            $response = $this->httpClient->patch($endpoint, [
                'query' => $params,
                'json' => $data
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new MaxMessengerException('PATCH request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Выполнить DELETE запрос
     */
    protected function delete(string $endpoint, array $params = []): array
    {
        $params['access_token'] = $this->config['bot_token'];
        
        try {
            $response = $this->httpClient->delete($endpoint, [
                'query' => $params
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new MaxMessengerException('DELETE request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Проверить ответ на ошибки
     */
    protected function checkResponse(array $response): array
    {
        if (isset($response['error'])) {
            throw new MaxMessengerException('API Error: ' . ($response['error']['message'] ?? 'Unknown error'));
        }
        
        return $response;
    }
}

