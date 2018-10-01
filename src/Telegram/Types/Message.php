<?php

namespace Antiflood\Telegram\Types;

/**
 * Class Message
 *
 * @package Antiflood\Telegram\Types
 */
class Message implements TypeInterface
{
    public const TEXT = 0;
    public const AUDIO = 1;
    public const DOCUMENT = 2;
    public const ANIMATION = 3;
    public const GAME = 4;
    public const PHOTO = 5;
    public const STICKER = 6;
    public const VIDEO = 7;
    public const VOICE = 8;
    public const VIDEO_NOTE = 9;
    public const CONTACT = 11;
    public const LOCATION = 12;
    public const VENUE = 13;
    public const NEW_CHAT_MEMBERS = 14;
    public const LEFT_CHAT_MEMBER = 15;
    public const NEW_CHAT_TITLE = 16;
    public const NEW_CHAT_PHOTO = 17;
    public const DELETE_CHAT_PHOTO = 18;
    public const GROUP_CHAT_CREATED = 19;
    public const SUPERGROUP_CHAT_CREATED = 20;
    public const CHANNEL_CHAT_CREATED = 21;
    public const MIGRATE_TO_CHAT_ID = 22;
    public const MIGRATE_FROM_CHAT_ID = 23;
    public const PINNED_MESSAGE = 24;
    public const INVOICE = 25;
    public const SUCCESSFUL_PAYMENT = 26;
    public const CONNECTED_WEBSITE = 27;

    /** @var int */
    private $type = self::TEXT;
    /** @var int */
    private $id;
    /** @var User */
    private $user;
    /** @var Chat */
    private $chat;
    /** @var string */
    private $text;
    /** @var int */
    private $date;

    /**
     * @param array $message
     *
     * @return Message
     */
    public static function parseMessage(?array $message): ?Message
    {
        return (new self())
            ->setId($message['message_id'] ?? null)
            ->setUser(User::parseUser($message['from'] ?? null))
            ->setChat(Chat::parseChat($message['chat'] ?? null))
            ->setText($message['text'] ?? null)
            ->setDate($message['date'] ?? null)
            ->parseMessageType($message)
            ;
    }

    /**
     * @param array $message
     *
     * @return self
     */
    private function parseMessageType(?array $message): self
    {
        if (null === $message) {
            return $this;
        }

        if (false === empty($message['audio'])) {
            $this->type = self::AUDIO;
        } elseif (false === empty($message['document'])) {
            $this->type = self::DOCUMENT;
        } elseif (false === empty($message['animation'])) {
            $this->type = self::ANIMATION;
        } elseif (false === empty($message['game'])) {
            $this->type = self::GAME;
        } elseif (false === empty($message['photo'])) {
            $this->type = self::PHOTO;
        } elseif (false === empty($message['sticker'])) {
            $this->type = self::STICKER;
        } elseif (false === empty($message['video'])) {
            $this->type = self::VIDEO;
        } elseif (false === empty($message['voice'])) {
            $this->type = self::VOICE;
        } elseif (false === empty($message['video_note'])) {
            $this->type = self::VIDEO_NOTE;
        } elseif (false === empty($message['contact'])) {
            $this->type = self::CONTACT;
        } elseif (false === empty($message['location'])) {
            $this->type = self::LOCATION;
        } elseif (false === empty($message['venue'])) {
            $this->type = self::VENUE;
        } elseif (false === empty($message['new_chat_members'])) {
            $this->type = self::NEW_CHAT_MEMBERS;
        } elseif (false === empty($message['left_chat_member'])) {
            $this->type = self::LEFT_CHAT_MEMBER;
        } elseif (false === empty($message['new_chat_title'])) {
            $this->type = self::NEW_CHAT_TITLE;
        } elseif (false === empty($message['new_chat_photo'])) {
            $this->type = self::NEW_CHAT_PHOTO;
        } elseif (false === empty($message['delete_chat_photo'])) {
            $this->type = self::DELETE_CHAT_PHOTO;
        } elseif (false === empty($message['group_chat_created'])) {
            $this->type = self::GROUP_CHAT_CREATED;
        } elseif (false === empty($message['supergroup_chat_created'])) {
            $this->type = self::SUPERGROUP_CHAT_CREATED;
        } elseif (false === empty($message['channel_chat_created'])) {
            $this->type = self::CHANNEL_CHAT_CREATED;
        } elseif (false === empty($message['migrate_to_chat_id'])) {
            $this->type = self::MIGRATE_TO_CHAT_ID;
        } elseif (false === empty($message['migrate_from_chat_id'])) {
            $this->type = self::MIGRATE_FROM_CHAT_ID;
        } elseif (false === empty($message['pinned_message'])) {
            $this->type = self::PINNED_MESSAGE;
        } elseif (false === empty($message['invoice'])) {
            $this->type = self::INVOICE;
        } elseif (false === empty($message['successful_payment'])) {
            $this->type = self::SUCCESSFUL_PAYMENT;
        } elseif (false === empty($message['connected_website'])) {
            $this->type = self::CONNECTED_WEBSITE;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
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

    /**
     * @param Chat $chat
     *
     * @return self
     */
    private function setChat(?Chat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * @return Chat
     */
    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    /**
     * @param string $text
     *
     * @return self
     */
    private function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param int $date
     *
     * @return self
     */
    private function setDate(?int $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getDate(): ?int
    {
        return $this->date;
    }
}
