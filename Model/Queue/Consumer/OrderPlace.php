<?php

namespace Vinhcd\AwsSns\Model\Queue\Consumer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSns\Model\SnsAdapter;

class OrderPlace
{
    /**
     * @var SnsAdapter
     */
    protected $snsAdapter;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SnsAdapter $snsAdapter
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        SnsAdapter $snsAdapter,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->snsAdapter = $snsAdapter;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param string $data
     * @return void
     */
    public function process($data)
    {
        try {
            $topicArn = $this->scopeConfig->getValue('vinhcd_aws/sns/order_place_arn');
            if ($topicArn) {
                $this->snsAdapter->sendToTopic($topicArn, $data);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
