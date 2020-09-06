<?php

namespace Vinhcd\AwsSns\Model;

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
            'region'      => 'ap-southeast-1',
            'credentials' => [
                'key'    => $scopeConfig->getValue('vinhcd_aws/api_key/access_key'),
                'secret' => $scopeConfig->getValue('vinhcd_aws/api_key/secret'),
            ],
        ]);
    }

    public function sendToTopic($topic='', $payload='')
    {
        return $this->client->listTopics();
    }
}
