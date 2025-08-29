<?php

namespace MaxMessenger\Services;

/**
 * Сервис для загрузки файлов
 * 
 * @see https://dev.max.ru/docs-api#upload
 */
class UploadService extends AbstractService
{
    /**
     * Получить URL для загрузки файла
     * 
     * @param array $options Дополнительные опции
     * @return array Информация о загрузке
     */
    public function getUrl(array $options = []): array
    {
        $response = $this->post('/upload', $options);
        return $this->checkResponse($response);
    }

    /**
     * Получить URL для загрузки изображения
     * 
     * @param string $filename Имя файла
     * @param int $size Размер файла в байтах
     * @return array Информация о загрузке
     */
    public function getImageUploadUrl(string $filename, int $size): array
    {
        return $this->getUrl([
            'type' => 'image',
            'filename' => $filename,
            'size' => $size
        ]);
    }

    /**
     * Получить URL для загрузки видео
     * 
     * @param string $filename Имя файла
     * @param int $size Размер файла в байтах
     * @return array Информация о загрузке
     */
    public function getVideoUploadUrl(string $filename, int $size): array
    {
        return $this->getUrl([
            'type' => 'video',
            'filename' => $filename,
            'size' => $size
        ]);
    }

    /**
     * Получить URL для загрузки аудио
     * 
     * @param string $filename Имя файла
     * @param int $size Размер файла в байтах
     * @return array Информация о загрузке
     */
    public function getAudioUploadUrl(string $filename, int $size): array
    {
        return $this->getUrl([
            'type' => 'audio',
            'filename' => $filename,
            'size' => $size
        ]);
    }

    /**
     * Получить URL для загрузки документа
     * 
     * @param string $filename Имя файла
     * @param int $size Размер файла в байтах
     * @return array Информация о загрузке
     */
    public function getDocumentUploadUrl(string $filename, int $size): array
    {
        return $this->getUrl([
            'type' => 'document',
            'filename' => $filename,
            'size' => $size
        ]);
    }

    /**
     * Получить URL для загрузки файла с произвольным типом
     * 
     * @param string $type Тип файла
     * @param string $filename Имя файла
     * @param int $size Размер файла в байтах
     * @return array Информация о загрузке
     */
    public function getCustomUploadUrl(string $type, string $filename, int $size): array
    {
        return $this->getUrl([
            'type' => $type,
            'filename' => $filename,
            'size' => $size
        ]);
    }

    /**
     * Загрузить файл на сервер MAX
     * 
     * @param string $filePath Путь к файлу
     * @param string $type Тип файла
     * @return array Результат загрузки
     */
    public function uploadFile(string $filePath, string $type = 'auto'): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $filename = basename($filePath);
        $size = filesize($filePath);

        // Получаем URL для загрузки
        $uploadInfo = $this->getUrl([
            'type' => $type === 'auto' ? $this->detectFileType($filename) : $type,
            'filename' => $filename,
            'size' => $size
        ]);

        // Загружаем файл на полученный URL
        $uploadUrl = $uploadInfo['upload_url'] ?? null;
        if (!$uploadUrl) {
            throw new \RuntimeException('Upload URL not received from API');
        }

        return $this->uploadToUrl($uploadUrl, $filePath);
    }

    /**
     * Загрузить файл на указанный URL
     * 
     * @param string $uploadUrl URL для загрузки
     * @param string $filePath Путь к файлу
     * @return array Результат загрузки
     */
    public function uploadToUrl(string $uploadUrl, string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->post($uploadUrl, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath)
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['error'])) {
                throw new \RuntimeException('Upload failed: ' . ($result['error']['message'] ?? 'Unknown error'));
            }

            return $result;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \RuntimeException('Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Автоматически определить тип файла по расширению
     * 
     * @param string $filename Имя файла
     * @return string Тип файла
     */
    private function detectFileType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'];
        
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        }
        
        return 'file';
    }

    /**
     * Получить информацию о загруженном файле
     * 
     * @param string $fileId ID файла
     * @return array Информация о файле
     */
    public function getFileInfo(string $fileId): array
    {
        $response = $this->get("/files/{$fileId}");
        return $this->checkResponse($response);
    }

    /**
     * Скачать файл
     * 
     * @param string $fileId ID файла
     * @param string $savePath Путь для сохранения
     * @return bool True если файл успешно скачан
     */
    public function downloadFile(string $fileId, string $savePath): bool
    {
        $fileInfo = $this->getFileInfo($fileId);
        $downloadUrl = $fileInfo['download_url'] ?? null;
        
        if (!$downloadUrl) {
            throw new \RuntimeException('Download URL not available');
        }

        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->get($downloadUrl);
            file_put_contents($savePath, $response->getBody()->getContents());
            return true;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \RuntimeException('Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Получить размер файла
     * 
     * @param string $fileId ID файла
     * @return int Размер файла в байтах
     */
    public function getFileSize(string $fileId): int
    {
        $fileInfo = $this->getFileInfo($fileId);
        return $fileInfo['size'] ?? 0;
    }

    /**
     * Получить MIME тип файла
     * 
     * @param string $fileId ID файла
     * @return string MIME тип
     */
    public function getFileMimeType(string $fileId): string
    {
        $fileInfo = $this->getFileInfo($fileId);
        return $fileInfo['mime_type'] ?? '';
    }

    /**
     * Проверить, является ли файл изображением
     * 
     * @param string $fileId ID файла
     * @return bool True если файл является изображением
     */
    public function isImage(string $fileId): bool
    {
        $mimeType = $this->getFileMimeType($fileId);
        return strpos($mimeType, 'image/') === 0;
    }

    /**
     * Проверить, является ли файл видео
     * 
     * @param string $fileId ID файла
     * @return bool True если файл является видео
     */
    public function isVideo(string $fileId): bool
    {
        $mimeType = $this->getFileMimeType($fileId);
        return strpos($mimeType, 'video/') === 0;
    }

    /**
     * Проверить, является ли файл аудио
     * 
     * @param string $fileId ID файла
     * @return bool True если файл является аудио
     */
    public function isAudio(string $fileId): bool
    {
        $mimeType = $this->getFileMimeType($fileId);
        return strpos($mimeType, 'audio/') === 0;
    }
}

