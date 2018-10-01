<?php

namespace Antiflood\Telegram;

use Antiflood\Telegram\Types\Message;

/**
 * Class Message
 *
 * @package Antiflood\Telegram\Types
 */
class Update
{
    /** @var int */
    private $id;
    /** @var Message */
    private $message;
    /** @var Message */
    private $editedMessage;

    /**
     * @param array $results
     *
     * @return array
     */
    public static function parseUpdates(?array $results): array
    {
        if (true === empty($results) || false === is_numeric(key($results))) {
            return [];
        }

        $updates = [];
        foreach ($results as $result) {
            $update = self::getUpdate($result);
            if (!$update instanceof Update) {
                continue;
            }

            $updates[] = $update;
        }

        return $updates;
    }

    /**
     * @param array $result
     *
     * @return Update
     */
    private static function getUpdate(array $result): ?Update
    {
        return (new self())
            ->setId($result['update_id'] ?? null)
            ->setMessage(Message::parseMessage($result['message'] ?? null))
            ->setEditedMessage(Message::parseMessage($result['edited_message'] ?? null))
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
     * @param Message $message
     *
     * @return self
     */
    private function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param Message $editedMessage
     *
     * @return self
     */
    private function setEditedMessage(Message $editedMessage): self
    {
        $this->editedMessage = $editedMessage;

        return $this;
    }

    /**
     * @return Message
     */
    public function getEditedMessage(): ?Message
    {
        return $this->editedMessage;
    }
}
