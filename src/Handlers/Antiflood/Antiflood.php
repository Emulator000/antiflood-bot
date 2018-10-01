<?php

namespace Antiflood\Handlers\Antiflood;

use Antiflood\Api;
use Antiflood\Handlers\Handler;
use Antiflood\Telegram\Types\Chat;
use Antiflood\Telegram\Types\Message;
use Antiflood\Telegram\Update;

/**
 * Class Antiflood
 *
 * @package Antiflood
 */
class Antiflood extends Handler
{
    /** @var int */
    private const TYPE_NOTHING = 0;
    /** @var int */
    private const TYPE_FLOOD = 1;

    /** @var array */
    private $shitsMessages = [];
    /** @var array */
    private $shitsJoins = [];
    /** @var array */
    private $globalShits = [];
    /** @var array */
    private $shitbans = [];
    /** @var array */
    private $deleted = [];
    /** @var array */
    private $alerts = [];

    /**
     * @param Api $api
     * @param Update $update
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Api $api, Update $update): void
    {
//        $api->sendMessage($update->getMessage()->getChat()->getId(), 'Test!');

        $antifloodResult = $this->antiflood($update);

        switch ($antifloodResult->getType()) {
            case self::TYPE_NOTHING:
                if (true === $antifloodResult->getAlert()) {
                    echo sprintf(
                        'Cooling down on chat %s',
                        var_export($update->getMessage()->getChat(), true)
                    ), PHP_EOL;
                }

                break;
            case self::TYPE_FLOOD:
                echo sprintf(
                    'Shitstorm or flood detected on chat %s! See details:',
                    var_export($update->getMessage()->getChat(), true)
                ), PHP_EOL;

                // Multi join detected
                if (false === empty($antifloodResult->getJoins())) {
                    foreach ($antifloodResult->getJoins() as $join) {
                        echo sprintf(
                            'Just shitbanned an %s',
                            var_export($join->getUser(), true)
                        ), PHP_EOL;

                        //ToDo:: ban user here!
                    }
                }

                // Flood detected
                if (false === empty($antifloodResult->getMessages())) {
                    foreach ($antifloodResult->getMessages() as $message) {
                        echo sprintf(
                            'Just deleted a message from %s',
                            var_export($message->getUser(), true)
                        ), PHP_EOL;

                        $api->deleteMessage($message->getChat()->getId(), $message->getId());
                    }
                }

                break;
        }
    }

    /**
     * @param Update $update
     *
     * @return AntifloodResult
     */
    private function antiflood(Update $update): AntifloodResult
    {
        $chatId = $update->getMessage()->getChat()->getId();

        if (false === isset($this->shitsJoins[$chatId])) {
            $this->shitsJoins[$chatId] = [];
        }

        if (false === isset($this->shitsMessages[$chatId])) {
            $this->shitsMessages[$chatId] = [];
        }

        if (false === isset($this->globalShits[$chatId])) {
            $this->globalShits[$chatId] = [];
        }

        if (false === isset($this->deleted[$chatId])) {
            $this->deleted[$chatId] = [];
        }

        if (false === isset($this->alerts[$chatId])) {
            $this->alerts[$chatId] = false;
        }

        /** @var Message[] $messages */
        $messages = &$this->shitsMessages[$chatId];
        /** @var Message[] $joins */
        $joins = &$this->shitsJoins[$chatId];
        /** @var Message[] $globalEvents */
        $globalEvents = &$this->globalShits[$chatId];
        /** @var int[] $deleted */
        $deleted = &$this->deleted[$chatId];
        /** @var bool $alert */
        $alert = &$this->alerts[$chatId];

        $users = [];
        $messages = array_filter(
            $messages,
            function (Message $message) use ($update, &$users) {
                $result = $this->isPassedEvent($update, $message, false);
                if (true === $result) {
                    $users[] = $message->getUser()->getId();
                }

                return $result;
            }
        );
        $joins = array_filter(
            $joins,
            function (Message $message) use ($update, &$users) {
                $result = $this->isPassedEvent($update, $message, true);
                if (true === $result) {
                    $users[] = $message->getUser()->getId();
                }

                return $result;
            }
        );

        $globalEvents = array_filter(
            array_filter(
                $globalEvents,
                function (Message $message) use ($update, &$users) {
                    return $this->isPassedEvent($update, $message, false);
                }
            ),
            function (Message $message) use ($update, &$users) {
                return $this->isPassedEvent($update, $message, true);
            }
        );

        if ($update->getMessage()->getType() === Message::NEW_CHAT_MEMBERS) {
            $joins[] = $update->getMessage();
        } else {
            $messages[] = $update->getMessage();
        }

        $globalEvents[] = $update->getMessage();

        $now = time();

        if (false === isset($this->shitbans[$chatId])) {
            $this->shitbans[$chatId] = [
                [],
                [],
            ];
        }

        /** @var array[] $shitbans */
        $shitbans = &$this->shitbans[$chatId];

        $outputJoins = [];
        $outputMessages = [];

        if (\count($joins) > $this->config->getJoinActions() && \count($users) >= $this->config->getMinimumUsers()) {
            foreach ($joins as $message) {
                $insert = false;
                if ($message->getType() === Message::NEW_CHAT_MEMBERS) {
                    if (true === empty($shitbans)) {
                        $insert = true;
                    } else {
                        $insert = true;
                        foreach ($shitbans[1] as $s) {
                            if ($s->getUser()->getId() === $message->getUser()->getId()) {
                                $insert = false;

                                break;
                            }
                        }
                    }
                }

                if (true === $insert) {
                    $shitbans[0][] = $now;
                }

                $shitbans[1][] = $message;
                $outputJoins[] = $message;
            }

            $alert = true;
        }

        if (\count($messages) > $this->config->getActions() && \count($users) >= $this->config->getMinimumUsers()) {
            foreach ($messages as $message) {
                $shitbans[1][] = $message;

                if (false === \in_array($message->getId(), $deleted, true)) {
                    $outputMessages[] = $message;
                }
            }

            $deleted = [];

            $alert = true;
        } elseif ($update->getMessage()->getChat()->getType() !== Chat::PRIVATE) {
            $badLen = false;
            if ($update->getMessage()->getType() === Message::TEXT) {
                $badLen = $this->config->getMessageLen() > 0
                    && strlen($update->getMessage()->getText()) > $this->config->getMessageLen();
            }

            if (true === $badLen) {
                if (\count($deleted) > 1000) {
                    $deleted = [];
                }

                $deleted[] = $update->getMessage()->getId();

                return (new AntifloodResult(self::TYPE_FLOOD))->setMessages(
                    [
                        $update->getMessage(),
                    ]
                );
            }
        }

        if (false === empty($outputMessages) || false === empty($outputJoins)) {
            return (new AntifloodResult(self::TYPE_FLOOD))
                ->setJoins($outputJoins)
                ->setMessages($outputMessages)
                ;
        }

        if (true === $alert && \count($globalEvents) <= $this->config->getActions()) {
            $alert = false;
        }

        return (new AntifloodResult(self::TYPE_NOTHING))
            ->setAlert($alert)
            ;
    }

    /**
     * @param Update $update
     * @param Message $message
     * @param bool $joinMode
     *
     * @return bool
     */
    private function isPassedEvent(Update $update, Message $message, bool $joinMode): bool
    {
        if (true === $joinMode) {
            if ($message->getType() === Message::NEW_CHAT_MEMBERS) {
                return ($update->getMessage()->getDate() - $message->getDate()) <= $this->config->getJoinSeconds();
            }
        } else {
            if ($message->getType() !== Message::NEW_CHAT_MEMBERS) {
                return ($update->getMessage()->getDate() - $message->getDate()) <= $this->config->getSeconds();
            }
        }

        //ToDo: check for media group and edited in order to avoid fake flood detection

        return false;
    }
}
