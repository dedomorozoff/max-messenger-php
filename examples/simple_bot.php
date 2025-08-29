<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MaxMessenger\MaxMessenger;
use MaxMessenger\Exceptions\MaxMessengerException;

// Конфигурация бота
$config = [
    'bot_token' => 'YOUR_BOT_TOKEN_HERE',
    'base_url' => 'https://botapi.max.ru',
    'timeout' => 30,
];

try {
    // Инициализация бота
    $bot = new MaxMessenger($config);
    
    echo "Бот инициализирован!\n";
    
    // Получить информацию о боте
    $botInfo = $bot->bot()->getInfo();
    echo "ID бота: " . $botInfo['user_id'] . "\n";
    echo "Имя бота: " . $botInfo['name'] . "\n";
    echo "Username: @" . $botInfo['username'] . "\n";
    
    // Получить список чатов
    $chats = $bot->chats()->getAll();
    echo "Количество чатов: " . count($chats['chats'] ?? []) . "\n";
    
    // Пример отправки сообщения (раскомментируйте и укажите chat_id)
    /*
    $result = $bot->messages()->sendText(
        'CHAT_ID_HERE',
        'Привет! Я бот на MAX Messenger!'
    );
    echo "Сообщение отправлено! ID: " . $result['message_id'] . "\n";
    */
    
    // Пример отправки сообщения с клавиатурой
    /*
    $buttons = [
        [
            $bot->messages()->createCallbackButton('Кнопка 1', 'action1'),
            $bot->messages()->createCallbackButton('Кнопка 2', 'action2')
        ],
        [
            $bot->messages()->createLinkButton('Сайт', 'https://example.com')
        ]
    ];
    
    $result = $bot->messages()->sendWithKeyboard(
        'CHAT_ID_HERE',
        'Выберите действие:',
        $buttons
    );
    echo "Сообщение с клавиатурой отправлено!\n";
    */
    
    // Пример загрузки файла
    /*
    $uploadResult = $bot->upload()->uploadFile(
        __DIR__ . '/test_image.jpg',
        'image'
    );
    echo "Файл загружен! ID: " . $uploadResult['file_id'] . "\n";
    */
    
    // Пример получения обновлений
    $updates = $bot->subscriptions()->getRecentUpdates(10);
    echo "Получено обновлений: " . count($updates['updates'] ?? []) . "\n";
    
    // Обработка обновлений
    foreach ($updates['updates'] ?? [] as $updateData) {
        $update = new \MaxMessenger\Objects\Update($updateData);
        
        if ($update->isMessage()) {
            $message = $update->getMessage();
            $text = $message->getText();
            $chatId = $message->getChat()->getId();
            $userId = $message->getFrom()->getId();
            
            echo "Новое сообщение от пользователя {$userId} в чате {$chatId}: {$text}\n";
            
            // Автоматический ответ на команду /start
            if ($text === '/start') {
                $bot->messages()->sendText($chatId, 'Привет! Я бот на MAX Messenger!');
                echo "Отправлен ответ на /start\n";
            }
        } elseif ($update->isCallbackQuery()) {
            $callbackData = $update->getCallbackData();
            $callbackId = $update->getCallbackId();
            $chatId = $update->getCallbackMessage()->getChat()->getId();
            
            echo "Callback: {$callbackData} от пользователя в чате {$chatId}\n";
            
            // Ответ на callback
            $bot->messages()->answerCallback($callbackId, "Вы нажали: {$callbackData}");
            echo "Отправлен ответ на callback\n";
        }
    }
    
} catch (MaxMessengerException $e) {
    echo "Ошибка MAX Messenger: " . $e->getMessage() . "\n";
    echo "HTTP код: " . $e->getHttpCode() . "\n";
    echo "Код ошибки API: " . $e->getApiErrorCode() . "\n";
} catch (\Exception $e) {
    echo "Общая ошибка: " . $e->getMessage() . "\n";
}
