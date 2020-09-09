<?php

namespace Vinhcd\AwsSns\Model;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SnsAdapter
{
    /**
     * @var SnsClient
     */
    protected $client;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * SnsAdapter constructor.
     * @param SnsClient $client
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;

        $this->client = new SnsClient([
            'version'     => 'latest',
            'region'      => $scopeConfig->getValue('vinhcd_aws/general/region'),
            'credentials' => [
                'key'    => $scopeConfig->getValue('vinhcd_aws/general/access_key'),
                'secret' => $scopeConfig->getValue('vinhcd_aws/general/secret'),
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
