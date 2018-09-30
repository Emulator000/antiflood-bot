<?php

namespace Antiflood\Telegram\Types;

/**
 * Class Chat
 *
 * @package Antiflood\Telegram\Types
 */
class Chat implements TypeInterface
{
    /** @var int */
    public const PRIVATE = 0;
    /** @var int */
    public const GROUP = 1;
    /** @var int */
    public const SUPERGROUP = 2;
    /** @var int */
    public const CHANNEL = 3;

    /** @var int */
    private $id;
    /** @var int */
    private $type;

    /**
     * @param array $chat
     *
     * @return Chat
     */
    public static function parseChat(?array $chat): ?Chat
    {
        return (new self())
            ->setId($chat['id'] ?? null)
            ->setType($chat['type'] ?? null)
            ;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    private function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    private function setType(?string $type): self
    {
        switch ($type) {
            case 'group':
                $this->type = self::GROUP;

                break;
            case 'supergroup':
                $this->type = self::SUPERGROUP;

                break;
            case 'channel':
                $this->type = self::CHANNEL;

                break;
            default:
                $this->type = self::PRIVATE;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }
}
