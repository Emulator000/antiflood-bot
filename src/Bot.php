<?php

namespace Antiflood;

use Antiflood\Telegram\UpdateInterface;

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
        $this->api = new Api(new Streamer($config->getTokens()));
        $this->api->onUpdate(function (UpdateInterface $update) {
            var_dump($update);
        });
        $this->api->listen();
    }
}
