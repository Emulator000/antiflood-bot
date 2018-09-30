<?php

namespace Antiflood\Telegram\Types;

/**
 * Class User
 *
 * @package Antiflood\Telegram\Types
 */
class User implements TypeInterface
{
    /** @var int */
    private $id;

    /**
     * @param array $user
     *
     * @return User
     */
    public static function parseUser(?array $user): ?User
    {
        return (new self())
            ->setId($user['id'] ?? null)
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
}
