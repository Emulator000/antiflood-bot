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
    /** @var GetUpdates **/
    private $getUpdates;

    /**
     * Api constructor.
     *
     * @param Streamer $streamer
     */
    public function __construct(Streamer $streamer)
    {
        $this->streamer = $streamer;
        $this->getUpdates = new GetUpdates();
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
     * @throws GuzzleException
     */
    public function getUpdates(): array
    {
        return $this->streamer->request($this->getUpdates);
    }
}
