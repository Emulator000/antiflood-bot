<?php

namespace Antiflood;

use Antiflood\Telegram\Methods\Request as TelegramRequest;
use Antiflood\Telegram\Update;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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
    /** @var string **/
    private $token;

    /**
     * Streamer constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->guzzleClient = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->getUri(),
            // You can set any number of default request options.
            'timeout'  => self::TIMEOUT,
        ]);
    }

    /**
     * @param string $methodName
     *
     * @return string
     */
    private function getUri(string $methodName = ''): string
    {
        return sprintf(
            '%sbot%s%s',
            self::URL,
            $this->token,
            false === empty($methodName) ? ('/'. $methodName) : ''
        );
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
        return $this->parseResponse(
            $this->guzzleClient->request(
                $request->getMethod(),
                $this->getUri($request->getName()),
                [
                    'form_params' => $request->getParams(),
                ]
            )
        );
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
}
