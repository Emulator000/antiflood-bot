<?php

namespace Antiflood;

use Antiflood\Telegram\Methods\GetUpdates;
use Antiflood\Telegram\Update;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Api
 *
 * @package Antiflood
 */
class Api
{
    /** @var Streamer **/
    private $streamer;
    /** @var callable[] **/
    private $callbacks;

    /** @var int */
    private $lastSavedId = 0;

    /**
     * Api constructor.
     *
     * @param Streamer $streamer
     */
    public function __construct(Streamer $streamer)
    {
        $this->streamer = $streamer;
    }

    public function listen(): void
    {
        while (true) {
            try {
                foreach ($this->getUpdates() as $update) {
                    foreach ($this->callbacks as $callback) {
                        $callback($update);
                    }
                }
            } catch (GuzzleException $e) {
                echo $e->getMessage(), PHP_EOL;
                echo $e->getTraceAsString(), PHP_EOL;
            }
        }
    }

    /**
     * @param callable $callback
     */
    public function onUpdate(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }

    /**
     * @return Update[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUpdates(): array
    {
        /** @var Update[] $updates */
        $updates = $this->streamer->request(new GetUpdates($this->lastSavedId));

        if (false === empty($updates)) {
            $this->lastSavedId = end($updates)->getId() + 1;
        }

        return $updates;
    }
}
