<?php

namespace Antiflood\Telegram\Methods;

/**
 * Class GetUpdates
 *
 * @package Antiflood\Telegram\Methods
 */
class DeleteMessage extends AbstractMethod
{
    /** @var string */
    private const METHOD_NAME = 'deleteMessage';

    /** @var int */
    private $chatId;
    /** @var int */
    private $messageId;

    /**
     * GetUpdates constructor.
     *
     * @param int $chatId
     * @param int $messageId
     */
    public function __construct(int $chatId, int $messageId)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::METHOD_NAME;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
        ];
    }
}
