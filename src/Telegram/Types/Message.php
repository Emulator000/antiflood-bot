<?php

namespace Antiflood\Telegram\Types;

/**
 * Class Message
 *
 * @package Antiflood\Telegram\Types
 */
class Message implements TypeInterface
{
    /** @var int */
    private $id;
    /** @var User */
    private $user;

    public static function parseMessage(?array $message): ?Message
    {
        return (new self())
            ->setId($message['message_id'] ?? null)
            ->setUser(User::parseUser($message['from'] ?? null))
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
     * @param User $user
     *
     * @return self
     */
    private function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
