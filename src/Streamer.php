<?php

namespace Antiflood;

use Antiflood\Telegram\Methods\Request as TelegramRequest;
use Antiflood\Telegram\Update;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
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
     * @param callable $callback
     *
     * @return Update[]
     *
     * @throws GuzzleException
     */
    public function request(TelegramRequest $request, ?callable $callback = null): array
    {
        $uri = $this->getUri($request->getName());
        $body = [
            'form_params' => $request->getParams(),
        ];

        if (true === $request->isAsync() || true === $request->isPool()) {
            if (true === $request->isAsync()) {
                $this->guzzleClient->requestAsync($request->getMethod(), $uri, $body)->then(
                    function (ResponseInterface $res) use ($callback) {
                        $callback($this->parseResponse($res));
                    },
                    function (RequestException $e) {
                        echo $e->getMessage(), PHP_EOL;
                        echo $e->getTraceAsString(), PHP_EOL;
                    }
                );

                return [];
            } elseif (true === $request->isPool()) {
                $pool = new Pool(
                    $this->guzzleClient,
                    (function () use ($request, $uri) {
                        while (true) {
                            yield new Request($request->getMethod(), $uri);
                        }
                    })(),
                    [
                        'concurrency' => 1,
                        'fulfilled' => function ($response, $index) use ($callback) {
                            $callback($this->parseResponse($response));
                        },
                        'rejected' => function ($reason, $index) {
                            echo 'Request failed!', PHP_EOL;
                            echo 'Reason: '. $reason, PHP_EOL;
                            echo 'Index: ' . $index, PHP_EOL;
                        },
                        'options' => $body,
                    ]
                );

                $promise = $pool->promise();
                $promise->wait();

                return [];
            }
        }

        return $this->parseResponse($this->guzzleClient->request($request->getMethod(), $uri, $body));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Update[]
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $response = json_decode((string)$response->getBody(), JSON_OBJECT_AS_ARRAY);

        $result = [];
        if (true === $response['ok']) {
            $result = $response['result'] ?? null;
            if (null !== $result && false === is_array($result)) {
                $result = [];
            }
        }

        return Update::parseUpdates($result);
    }
}
