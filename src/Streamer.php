<?php

namespace Antiflood;

use Antiflood\Telegram\Methods\Request as TelegramRequest;
use Antiflood\Telegram\Types\Chat;
use Antiflood\Telegram\Update;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use iter;

/**
 * Class Streamer
 *
 * @package Antiflood
 */
class Streamer
{
    /** @var string **/
    private const URL = 'https://api.telegram.org/';
    /** @var float **/
    private const TIMEOUT = 0;

    /** @var Client **/
    private $guzzleClient;
    /** @var string[] **/
    private $tokens;

    /** @var string[] */
    private $updateIds = [];

    /**
     * Streamer constructor.
     *
     * @param string[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->guzzleClient = new Client([
            'timeout'  => self::TIMEOUT,
        ]);
    }

    /**
     * @param string $methodName
     *
     * @return \Iterator
     */
    private function getUri(string $methodName = ''): \Iterator
    {
        foreach ($this->tokens as $token) {
            yield sprintf(
                '%sbot%s%s',
                self::URL,
                $token,
                false === empty($methodName) ? ('/'. $methodName) : ''
            );
        }
    }

    /**
     * @param TelegramRequest $request
     *
     * @return Update[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(TelegramRequest $request): array
    {
        $updates = [];
        foreach ($this->getUri($request->getName()) as $botIndex => $uri) {
            $newUpdates = $this->parseResponse(
                $this->guzzleClient->request(
                    $request->getMethod(),
                    $uri,
                    [
                        'form_params' => $request->getParams(),
                    ]
                )
            );

            $request->handleUpdates($newUpdates, $botIndex);

            $updates = array_merge($updates, $this->filterDuplicated($newUpdates));

            $this->updateIds = array_merge($this->updateIds, $this->generateUpdateIds($updates));
        }

        return $updates;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Update[]
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $response = json_decode((string)$response->getBody(), JSON_OBJECT_AS_ARRAY);

        return true === $response['ok'] ? Update::parseUpdates($response['result'] ?? null) : [];
    }

    /**
     * @param Update[] $updates
     *
     * @return array
     */
    private function generateUpdateIds(array $updates): array
    {
        $updatedIds = [];
        foreach ($updates as $update) {
            $message = $update->getMessage();
//            $editedMessage = $update->getEditedMessage();

            if (null !== $message) {
                $chat = $message->getChat();
                $userId = null !== $message->getUser() ? $message->getUser()->getId() : 0;

                if (null !== $chat) {
                    if (Chat::GROUP == $chat->getType()) {
                        $updatedIds[] = sprintf(
                            '(%d,%d,%s)',
                            $chat->getId(),
                            $userId
                        );
                    } else {
                        $updatedIds[] = sprintf(
                            '(%d,%d)',
                            $chat->getId(),
                            $userId
                        );
                    }
                } else {
                    $updatedIds[] = 0;
                }
            } else {
                $chat = $message->getChat();
                $userId = null !== $message->getUser() ? $message->getUser()->getId() : 0;

                if (null !== $chat) {
                    $updatedIds[] = sprintf(
                        '(%d,%d)',
                        $chat->getId(),
                        $userId
                    );
                } else {
                    $updatedIds[] = 0;
                }
            }
        }

        return $updatedIds;
    }

    /**
     * @param Update[] $updates
     *
     * @return Update[]
     */
    private function filterDuplicated(array $updates): array
    {
        $updatedIds = $this->generateUpdateIds($updates);
        foreach ($updatedIds as $index => $newUpdatedId) {
            foreach ($this->updateIds as $updateId) {
                if ($updateId === $newUpdatedId) {
//                    echo sprintf('Rimuovo %s perché già presente!', $updateId), PHP_EOL;
                    unset($updates[$index]);
                }
            }
        }

        return $updates;
    }
}
