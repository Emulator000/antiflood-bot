<?php

namespace Antiflood\Handlers\Antiflood;

use Antiflood\Telegram\Types\Message;

/**
 * Class AntifloodResult
 *
 * @package Antiflood\Handlers\Antiflood
 */
class AntifloodResult
{
    /** @var int */
    public const TYPE_NOTHING = 0;
    /** @var int */
    public const TYPE_FLOOD = 1;

    /** @var int */
    private $type = self::TYPE_NOTHING;
    /** @var Message[] */
    private $joins = [];
    /** @var Message[] */
    private $messages = [];
    /** @var bool */
    private $alert = false;

    /**
     * AntifloodResult constructor.
     *
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param Message[] $messages
     *
     * @return self
     */
    public function setJoins(array $messages): self
    {
        $this->joins = $messages;
        
        return $this;
    }

    /**
     * @return Message[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @param Message[] $messages
     *
     * @return self
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        
        return $this;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param bool $alert
     *
     * @return self
     */
    public function setAlert(bool $alert): self
    {
        $this->alert = $alert;
        
        return $this;
    }

    /**
     * @return bool
     */
    public function getAlert(): bool
    {
        return $this->alert;
    }
}
