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
    /** @var array */
    private $yamlRaw;
    /** @var string[] */
    private $tokens;

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
    }

    /**
     * @return string[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
