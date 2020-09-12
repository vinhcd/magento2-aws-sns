<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSns\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSns\Model\Config\Config;
use Vinhcd\AwsSns\Model\ResourceModel\SnsQueue\Collection;
use Vinhcd\AwsSns\Model\ResourceModel\SnsQueue\CollectionFactory;
use Vinhcd\AwsSns\Model\SnsAdapter;
use Vinhcd\AwsSns\Model\SnsQueue;

class SendSnsQueueJob
{
    /**
     * @var SnsAdapter
     */
    protected $snsAdapter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendSnsQueueJob constructor.
     * @param SnsAdapter $snsAdapter
     * @param CollectionFactory $collectionFactory
     * @param Config $config
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        SnsAdapter $snsAdapter,
        CollectionFactory $collectionFactory,
        Config $config,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->snsAdapter = $snsAdapter;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->removeDeadQueues();

        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $i = 0;
        /* @var SnsQueue $queue */
        foreach ($collection as $queue) {
            try {
                $this->snsAdapter->sendToTopic($queue->getTopicArn(), $queue->getMessage());
                $queue->delete();
                $i++;
            } catch (\Exception $e) {
                $this->logger->critical('SNS Queue error:');
                $this->logger->critical($e->getMessage());
            }
            if ($i >= $this->config->getMaxMessagePerQueue()) {
                break;
            }
        }
    }

    /**
     * @return void
     */
    protected function removeDeadQueues()
    {
        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $deadQueueDays = '-' . $this->config->getDeadQueueDays() . ' days';
        try {
            $collection->addFieldToFilter(
                'created_at',
                ['lt' => $this->dateTime->date('Y-m-d H:i:s', strtotime($deadQueueDays))]
            )->walk('delete');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
