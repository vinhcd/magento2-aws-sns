<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSns\Model;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;
use Vinhcd\AwsSns\Model\Config\Config;

class SnsAdapter
{
    /**
     * @var SnsClient
     */
    protected $client;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SnsAdapter constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->client = new SnsClient([
            'version'     => 'latest',
            'region'      => $config->getDefaultRegion(),
            'credentials' => [
                'key'    => $config->getAccessKey(),
                'secret' => $config->getSecret()
            ],
        ]);
    }

    /**
     * @return \Aws\Result
     * @throws AwsException
     */
    public function getTopics()
    {
        return $this->client->listTopics();
    }

    /**
     * @param string $topic
     * @param string $payload
     * @return void
     * @throws AwsException
     */
    public function sendToTopic($topic, $payload)
    {
        $this->client->publish([
            'Message' => $payload,
            'TopicArn' => $topic,
        ]);
    }
}
