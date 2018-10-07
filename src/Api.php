<?php

namespace Antiflood;

use Antiflood\Telegram\Methods\DeleteMessage;
use Antiflood\Telegram\Methods\GetUpdates;
use Antiflood\Telegram\Methods\SendMessage;
use Antiflood\Telegram\Types\Chat;
use Antiflood\Telegram\Update;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Api
 *
 * @package Antiflood
 */
class Api
{
    /** @var int */
    private const UPDATES_LIMIT = 10000;

    /** @var Streamer[] **/
    private $streamers;
    /** @var callable[] **/
    private $callbacks;

    /** @var int[] */
    private $currentIndex = [];
    /** @var int[] */
    private $savedIds = [];
    /** @var int[] */
    private $updateIds = [];

    /**
     * Api constructor.
     *
     * @param Streamer[] $streamers
     */
    public function __construct(array $streamers)
    {
        $this->streamers = $streamers;
    }

    public function listen(): void
    {
        try {
            $this->getUpdates(function ($updates) {
                foreach ($updates as $update) {
                    foreach ($this->callbacks as $callback) {
                        $callback($update);
                    }
                }
            });
        } catch (GuzzleException $e) {
            echo $e->getMessage(), PHP_EOL;
            echo $e->getTraceAsString(), PHP_EOL;
        }
    }

    /**
     * @param callable $callback
     */
    public function onUpdate(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }

    /**
     * @param int $chatId
     * @param string $text
     * @param callable $callback
     *
     * @throws GuzzleException
     */
    public function sendMessage(int $chatId, string $text, ?callable $callback = null): void
    {
        $this->getStreamer($chatId)->request(new SendMessage($chatId, $text), $callback);
    }

    /**
     * @param int $chatId
     * @param int $messageId
     * @param callable $callback
     *
     * @throws GuzzleException
     */
    public function deleteMessage(int $chatId, int $messageId, ?callable $callback = null): void
    {
        $this->getStreamer($chatId)->request(new DeleteMessage($chatId, $messageId), $callback);
    }

    /**
     * @param int $chatId
     *
     * @return Streamer
     */
    private function getStreamer(int $chatId): Streamer
    {
        if (false === isset($this->currentIndex[$chatId])) {
            $this->currentIndex[$chatId] = 0;
        }

        return $this->streamers[$this->currentIndex[$chatId]++ % \count($this->streamers)];
    }

    /**
     * @param callable $callback
     *
     * @throws GuzzleException
     */
    private function getUpdates(callable $callback): void
    {
        foreach ($this->streamers as $index => $streamer) {
            $streamer->request(
                new GetUpdates($this->savedIds[$index] ?? 0),
                function ($updates) use ($index, $callback) {
                    if (false === empty($updates)) {
                        $this->savedIds[$index] = end($updates)->getId() ?? 0;
                    }

                    var_dump($this->savedIds);
                    $callback($this->filterDuplicated($updates));
                }
            );
        }
    }

    /**
     * @param Update $update
     *
     * @return string
     */
    private function generateUpdateId(Update $update): string
    {
        $message = $update->getMessage() ?? $update->getEditedMessage();
        if (null !== $message) {
            $chat = $message->getChat();
            $userId = null !== $message->getUser() ? $message->getUser()->getId() : 0;

            if (null !== $chat) {
                if (Chat::GROUP == $chat->getType()) {
                    return sprintf(
                        '(%d,%d,%s)',
                        $chat->getId(),
                        $userId,
                        $message->getDate()
                    );
                } else {
                    return sprintf(
                        '(%d,%d)',
                        $message->getId(),
                        $userId
                    );
                }
            }
        } else {
            $chat = $message->getChat();
            $userId = null !== $message->getUser() ? $message->getUser()->getId() : 0;

            if (null !== $chat) {
                return sprintf(
                    '(%d,%d)',
                    $message->getId(),
                    $userId
                );
            }
        }

        return '0';
    }

    /**
     * @param Update[] $updates
     *
     * @return Update[]
     */
    private function filterDuplicated(array $updates): array
    {
        $updates = array_filter(
            $updates,
            function ($update) {
                $updateId = $this->generateUpdateId($update);
                if (false === isset($this->updateIds[$updateId])) {
                    $this->updateIds[$updateId] = 0;

                    return true;
                }

                return false;
            }
        );

        if (\count($this->updateIds) >= self::UPDATES_LIMIT) {
            $this->updateIds = [];
        }

        return $updates;
    }
}
