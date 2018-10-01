<?php

namespace Antiflood;

use Antiflood\Telegram\Update;

/**
 * Class Bot
 *
 * @package Antiflood
 */
class Bot
{
    /** @var Api **/
    private $api;

    /**
     * Bot constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->api = new Api(
            array_map(
                function (string $token) {
                    return new Streamer($token);
                },
                $config->getTokens()
            )
        );

        $this->api->onUpdate(function (Update $update) {
            $this->api->sendMessage($update->getMessage()->getChat()->getId(), 'Test!');
        });

        $this->api->listen();
    }
}
