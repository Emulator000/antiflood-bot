<?php

namespace Antiflood;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @package Antiflood
 */
class Config
{
    private const DEFAULT_MESSAGE_LEN = 0;
    private const DEFAULT_SECONDS = 1;
    private const DEFAULT_ACTIONS = 5;
    private const DEFAULT_JOIN_SECONDS = 1;
    private const DEFAULT_JOIN_ACTIONS = 3;
    private const DEFAULT_MINIMUM_USERS = 1;

    /** @var array */
    private $yamlRaw;
    /** @var string[] */
    private $tokens;
    /** @var int */
    private $messageLen = self::DEFAULT_MESSAGE_LEN;
    /** @var int */
    private $seconds = self::DEFAULT_SECONDS;
    /** @var int */
    private $actions = self::DEFAULT_ACTIONS;
    /** @var int */
    private $joinSeconds = self::DEFAULT_JOIN_SECONDS;
    /** @var int */
    private $joinActions = self::DEFAULT_JOIN_ACTIONS;
    /** @var int */
    private $minimumUsers = self::DEFAULT_MINIMUM_USERS;

    /**
     * Config constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $yamlPath = __DIR__ . '/../config/config_local.yaml';
        if (false === file_exists($yamlPath)) {
            throw new \Exception(
                'Couldn\'t load config file! Please create a "config_local.yaml" that extends the "config.yaml" file.'
            );
        }

        $this->yamlRaw = Yaml::parseFile($yamlPath);

        $this->loadConfig();
    }

    private function loadConfig()
    {
        if (false === empty($this->yamlRaw['imports'])) {
            foreach ($this->yamlRaw['imports'] as $config) {
                $this->yamlRaw += Yaml::parseFile(__DIR__ . '/../config/' . $config['resource']);
            }
        }

        if (false === empty($this->yamlRaw['bot']['token'])) {
            $this->tokens = $this->yamlRaw['bot']['token'];
        }

        if (false === empty($this->yamlRaw['bot']['message_len'])) {
            $this->messageLen = $this->yamlRaw['bot']['message_len'];
        }

        if (false === empty($this->yamlRaw['bot']['seconds'])) {
            $this->seconds = $this->yamlRaw['bot']['seconds'];
        }

        if (false === empty($this->yamlRaw['bot']['actions'])) {
            $this->actions = $this->yamlRaw['bot']['actions'];
        }

        if (false === empty($this->yamlRaw['bot']['join_seconds'])) {
            $this->joinSeconds = $this->yamlRaw['bot']['join_seconds'];
        }

        if (false === empty($this->yamlRaw['bot']['join_actions'])) {
            $this->joinActions = $this->yamlRaw['bot']['join_actions'];
        }

        if (false === empty($this->yamlRaw['bot']['minimum_users'])) {
            $this->minimumUsers = $this->yamlRaw['bot']['minimum_users'];
        }
    }

    /**
     * @return string[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return int
     */
    public function getMessageLen(): int
    {
        return $this->messageLen;
    }

    /**
     * @return int
     */
    public function getSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * @return int
     */
    public function getActions(): int
    {
        return $this->actions;
    }

    /**
     * @return int
     */
    public function getJoinSeconds(): int
    {
        return $this->joinSeconds;
    }

    /**
     * @return int
     */
    public function getJoinActions(): int
    {
        return $this->joinActions;
    }

    /**
     * @return int
     */
    public function getMinimumUsers(): int
    {
        return $this->minimumUsers;
    }
}
