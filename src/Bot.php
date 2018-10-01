<?php

namespace Antiflood;

use Antiflood\Handlers\Antiflood\Antiflood;
use Antiflood\Handlers\HandlerInterface;
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
    /** @var HandlerInterface[] **/
    private $handlers;

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

        $this->handlers[] = new Antiflood($config);

        foreach ($this->handlers as $handler) {
            $this->api->onUpdate(function (Update $update) use ($handler) {
                $handler->handle($this->api, $update);
            });
        }

        $this->api->listen();
    }
}
