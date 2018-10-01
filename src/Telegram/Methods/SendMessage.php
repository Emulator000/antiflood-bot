<?php

namespace Antiflood\Telegram\Methods;

/**
 * Class GetUpdates
 *
 * @package Antiflood\Telegram\Methods
 */
class SendMessage extends AbstractMethod
{
    /** @var string */
    private const METHOD_NAME = 'sendMessage';

    /** @var int */
    private $chatId;
    /** @var string */
    private $text;

    /**
     * GetUpdates constructor.
     *
     * @param int $chatId
     * @param string $text
     */
    public function __construct(int $chatId, string $text)
    {
        $this->chatId = $chatId;
        $this->text = $text;
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
            'text' => $this->text,
        ];
    }
}
