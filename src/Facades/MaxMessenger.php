<?php

namespace MaxMessenger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MaxMessenger\Services\BotService bot()
 * @method static \MaxMessenger\Services\ChatsService chats()
 * @method static \MaxMessenger\Services\MessagesService messages()
 * @method static \MaxMessenger\Services\SubscriptionsService subscriptions()
 * @method static \MaxMessenger\Services\UploadService upload()
 * @method static array getConfig()
 * @method static \GuzzleHttp\Client getHttpClient()
 * 
 * @see \MaxMessenger\MaxMessenger
 */
class MaxMessenger extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'max-messenger';
    }
}
