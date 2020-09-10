<?php

namespace Vinhcd\AwsSns\Cron;

use Psr\Log\LoggerInterface;
use Vinhcd\AwsSns\Model\ResourceModel\SnsQueue\Collection;
use Vinhcd\AwsSns\Model\ResourceModel\SnsQueue\CollectionFactory;
use Vinhcd\AwsSns\Model\SnsAdapter;
use Vinhcd\AwsSns\Model\SnsQueue;

class SendSnsQueueJob
{
    /**
     * todo: move to configurable option
     * @var int
     */
    const MAX_SEND = 10;

    /**
     * @var SnsAdapter
     */
    protected $snsAdapter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendSnsQueueJob constructor.
     * @param SnsAdapter $snsAdapter
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SnsAdapter $snsAdapter,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->snsAdapter = $snsAdapter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute()
    {
        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $i = 0;
        /* @var SnsQueue $queue */
        foreach ($collection as $queue) {
            try {
                $this->snsAdapter->sendToTopic($queue->getTopicArn(), $queue->getMessage());
                $queue->delete();
            } catch (\Exception $e) {
                $this->logger->critical('SNS Queue error:');
                $this->logger->critical($e->getMessage());
            }
            if ($i >= self::MAX_SEND) break;
        }
    }
}
