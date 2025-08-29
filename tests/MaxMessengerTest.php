<?php

namespace MaxMessenger\Tests;

use PHPUnit\Framework\TestCase;
use MaxMessenger\MaxMessenger;
use MaxMessenger\Exceptions\MaxMessengerException;
use MaxMessenger\Objects\User;
use MaxMessenger\Objects\Chat;
use MaxMessenger\Objects\Message;
use MaxMessenger\Objects\Update;

class MaxMessengerTest extends TestCase
{
    private MaxMessenger $bot;

    protected function setUp(): void
    {
        $this->bot = new MaxMessenger([
            'bot_token' => 'test_token',
            'base_url' => 'https://test.max.ru',
        ]);
    }

    public function testBotInitialization()
    {
        $this->assertInstanceOf(MaxMessenger::class, $this->bot);
        $this->assertEquals('test_token', $this->bot->getConfig()['bot_token']);
        $this->assertEquals('https://test.max.ru', $this->bot->getConfig()['base_url']);
    }

    public function testBotInitializationWithoutToken()
    {
        $this->expectException(MaxMessengerException::class);
        $this->expectExceptionMessage('Bot token is required');
        
        new MaxMessenger([]);
    }

    public function testBotService()
    {
        $botService = $this->bot->bot();
        $this->assertInstanceOf(\MaxMessenger\Services\BotService::class, $botService);
    }

    public function testMessagesService()
    {
        $messagesService = $this->bot->messages();
        $this->assertInstanceOf(\MaxMessenger\Services\MessagesService::class, $messagesService);
    }

    public function testChatsService()
    {
        $chatsService = $this->bot->chats();
        $this->assertInstanceOf(\MaxMessenger\Services\ChatsService::class, $chatsService);
    }

    public function testSubscriptionsService()
    {
        $subscriptionsService = $this->bot->subscriptions();
        $this->assertInstanceOf(\MaxMessenger\Services\SubscriptionsService::class, $subscriptionsService);
    }

    public function testUploadService()
    {
        $uploadService = $this->bot->upload();
        $this->assertInstanceOf(\MaxMessenger\Services\UploadService::class, $uploadService);
    }

    public function testUserObject()
    {
        $userData = [
            'user_id' => 123,
            'name' => 'Test User',
            'username' => 'testuser',
            'is_bot' => false,
            'last_activity_time' => 1640995200000
        ];

        $user = new User($userData);

        $this->assertEquals(123, $user->getId());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertFalse($user->isBot());
        $this->assertEquals(1640995200000, $user->getLastActivityTime());
        $this->assertEquals('max://max.ru/testuser', $user->getLink());
    }

    public function testChatObject()
    {
        $chatData = [
            'chat_id' => 'chat123',
            'type' => 'group',
            'title' => 'Test Group',
            'description' => 'Test description',
            'photo' => null,
            'permissions' => [
                'can_send_messages' => true,
                'can_send_media_messages' => true
            ],
            'slow_mode_delay' => null,
            'invite_link' => null,
            'pinned_message_id' => null,
            'sticker_set_name' => null,
            'can_set_sticker_set' => null
        ];

        $chat = new Chat($chatData);

        $this->assertEquals('chat123', $chat->getId());
        $this->assertEquals('group', $chat->getType());
        $this->assertEquals('Test Group', $chat->getTitle());
        $this->assertEquals('Test description', $chat->getDescription());
        $this->assertTrue($chat->isGroup());
        $this->assertFalse($chat->isPrivate());
        $this->assertFalse($chat->isChannel());
        $this->assertTrue($chat->canSendMessages());
        $this->assertTrue($chat->canSendMediaMessages());
    }

    public function testMessageObject()
    {
        $messageData = [
            'message_id' => 456,
            'from' => [
                'user_id' => 123,
                'name' => 'Test User',
                'username' => 'testuser',
                'is_bot' => false,
                'last_activity_time' => 1640995200000
            ],
            'chat' => [
                'chat_id' => 'chat123',
                'type' => 'private',
                'title' => 'Test User',
                'description' => null,
                'photo' => null,
                'permissions' => [],
                'slow_mode_delay' => null,
                'invite_link' => null,
                'pinned_message_id' => null,
                'sticker_set_name' => null,
                'can_set_sticker_set' => null
            ],
            'date' => 1640995200,
            'text' => 'Hello, world!',
            'attachments' => [],
            'reply_to_message' => null,
            'edit_date' => null,
            'author_signature' => null,
            'forward_signature' => null,
            'forward_date' => null,
            'is_automatic_forward' => null,
            'via_bot' => null,
            'media_group_id' => null,
            'entities' => []
        ];

        $message = new Message($messageData);

        $this->assertEquals(456, $message->getId());
        $this->assertEquals('Hello, world!', $message->getText());
        $this->assertEquals(1640995200, $message->getDate());
        $this->assertTrue($message->isText());
        $this->assertFalse($message->isReply());
        $this->assertFalse($message->isEdited());
        $this->assertFalse($message->isForward());
        $this->assertFalse($message->hasAttachments());
        $this->assertFalse($message->hasMediaGroup());
    }

    public function testUpdateObject()
    {
        $updateData = [
            'update_id' => 789,
            'message' => [
                'message_id' => 456,
                'from' => [
                    'user_id' => 123,
                    'name' => 'Test User',
                    'username' => 'testuser',
                    'is_bot' => false,
                    'last_activity_time' => 1640995200000
                ],
                'chat' => [
                    'chat_id' => 'chat123',
                    'type' => 'private',
                    'title' => 'Test User',
                    'description' => null,
                    'photo' => null,
                    'permissions' => [],
                    'slow_mode_delay' => null,
                    'invite_link' => null,
                    'pinned_message_id' => null,
                    'sticker_set_name' => null,
                    'can_set_sticker_set' => null
                ],
                'date' => 1640995200,
                'text' => 'Hello, world!',
                'attachments' => [],
                'reply_to_message' => null,
                'edit_date' => null,
                'author_signature' => null,
                'forward_signature' => null,
                'forward_date' => null,
                'is_automatic_forward' => null,
                'via_bot' => null,
                'media_group_id' => null,
                'entities' => []
            ],
            'edited_message' => null,
            'channel_post' => null,
            'edited_channel_post' => null,
            'callback_query' => null,
            'chat_member' => null,
            'chat_join_request' => null
        ];

        $update = new Update($updateData);

        $this->assertEquals(789, $update->getId());
        $this->assertTrue($update->isMessage());
        $this->assertFalse($update->isCallbackQuery());
        $this->assertEquals('message', $update->getType());
        $this->assertNotNull($update->getMessage());
        $this->assertEquals('chat123', $update->getChatId());
        $this->assertEquals(123, $update->getUserId());
        $this->assertEquals('Hello, world!', $update->getText());
    }

    public function testUpdateWithCallbackQuery()
    {
        $updateData = [
            'update_id' => 789,
            'message' => null,
            'edited_message' => null,
            'channel_post' => null,
            'edited_channel_post' => null,
            'callback_query' => [
                'id' => 'callback123',
                'from' => [
                    'user_id' => 123,
                    'name' => 'Test User',
                    'username' => 'testuser',
                    'is_bot' => false,
                    'last_activity_time' => 1640995200000
                ],
                'message' => [
                    'message_id' => 456,
                    'from' => [
                        'user_id' => 456,
                        'name' => 'Bot',
                        'username' => 'testbot',
                        'is_bot' => true,
                        'last_activity_time' => 1640995200000
                    ],
                    'chat' => [
                        'chat_id' => 'chat123',
                        'type' => 'private',
                        'title' => 'Test User',
                        'description' => null,
                        'photo' => null,
                        'permissions' => [],
                        'slow_mode_delay' => null,
                        'invite_link' => null,
                        'pinned_message_id' => null,
                        'sticker_set_name' => null,
                        'can_set_sticker_set' => null
                    ],
                    'date' => 1640995200,
                    'text' => 'Test message',
                    'attachments' => [],
                    'reply_to_message' => null,
                    'edit_date' => null,
                    'author_signature' => null,
                    'forward_signature' => null,
                    'forward_date' => null,
                    'is_automatic_forward' => null,
                    'via_bot' => null,
                    'media_group_id' => null,
                    'entities' => []
                ],
                'data' => 'test_action'
            ],
            'chat_member' => null,
            'chat_join_request' => null
        ];

        $update = new Update($updateData);

        $this->assertTrue($update->isCallbackQuery());
        $this->assertEquals('callback_query', $update->getType());
        $this->assertEquals('test_action', $update->getCallbackData());
        $this->assertEquals('callback123', $update->getCallbackId());
        $this->assertNotNull($update->getCallbackUser());
        $this->assertNotNull($update->getCallbackMessage());
    }

    public function testMessageServiceHelpers()
    {
        $messagesService = $this->bot->messages();

        // Тест создания кнопок
        $callbackButton = $messagesService->createCallbackButton('Test', 'test_action');
        $this->assertEquals('callback', $callbackButton['type']);
        $this->assertEquals('Test', $callbackButton['text']);
        $this->assertEquals('test_action', $callbackButton['payload']);

        $linkButton = $messagesService->createLinkButton('Link', 'https://example.com');
        $this->assertEquals('link', $linkButton['type']);
        $this->assertEquals('Link', $linkButton['text']);
        $this->assertEquals('https://example.com', $linkButton['url']);

        $contactButton = $messagesService->createContactButton('Contact');
        $this->assertEquals('request_contact', $contactButton['type']);
        $this->assertEquals('Contact', $contactButton['text']);

        $locationButton = $messagesService->createLocationButton('Location');
        $this->assertEquals('request_geo_location', $locationButton['type']);
        $this->assertEquals('Location', $locationButton['text']);

        $appButton = $messagesService->createAppButton('App', 'app123');
        $this->assertEquals('open_app', $appButton['type']);
        $this->assertEquals('App', $appButton['text']);
        $this->assertEquals('app123', $appButton['app_id']);

        $messageButton = $messagesService->createMessageButton('Message', 'Hello');
        $this->assertEquals('message', $messageButton['type']);
        $this->assertEquals('Message', $messageButton['text']);
        $this->assertEquals('Hello', $messageButton['message']);
    }

    public function testExceptionCreation()
    {
        $exception = MaxMessengerException::authenticationFailed('Custom message');
        $this->assertEquals('Custom message', $exception->getMessage());
        $this->assertEquals(401, $exception->getHttpCode());
        $this->assertEquals('AUTHENTICATION_FAILED', $exception->getApiErrorCode());

        $exception = MaxMessengerException::notFound();
        $this->assertEquals('Resource not found', $exception->getMessage());
        $this->assertEquals(404, $exception->getHttpCode());
        $this->assertEquals('NOT_FOUND', $exception->getApiErrorCode());

        $exception = MaxMessengerException::rateLimitExceeded();
        $this->assertEquals('Rate limit exceeded', $exception->getMessage());
        $this->assertEquals(429, $exception->getHttpCode());
        $this->assertEquals('RATE_LIMIT_EXCEEDED', $exception->getApiErrorCode());
    }

    public function testObjectSerialization()
    {
        $userData = [
            'user_id' => 123,
            'name' => 'Test User',
            'username' => 'testuser',
            'is_bot' => false,
            'last_activity_time' => 1640995200000
        ];

        $user = new User($userData);
        $serialized = $user->toArray();
        $deserialized = User::fromArray($serialized);

        $this->assertEquals($user->getId(), $deserialized->getId());
        $this->assertEquals($user->getName(), $deserialized->getName());
        $this->assertEquals($user->getUsername(), $deserialized->getUsername());
        $this->assertEquals($user->isBot(), $deserialized->isBot());
        $this->assertEquals($user->getLastActivityTime(), $deserialized->getLastActivityTime());
    }
}
