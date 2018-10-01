<?php

namespace Antiflood\Handlers;

use Antiflood\Config;

/**
 * Class Handler
 *
 * @package Antiflood\Handlers
 */
abstract class Handler implements HandlerInterface
{
    /** @var Config */
    protected $config;

    /**
     * Handler constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
}
