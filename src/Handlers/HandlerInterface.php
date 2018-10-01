<?php

namespace Antiflood\Handlers;

use Antiflood\Api;
use Antiflood\Telegram\Update;

/**
 * Interface HandlerInterface
 *
 * @package Antiflood\Handlers
 */
interface HandlerInterface
{
    /**
     * @param Api $api
     * @param Update $update
     */
    public function handle(Api $api, Update $update): void;
}
