<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSns\Observer;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSns\Model\Config\Config;
use Vinhcd\AwsSns\Model\SnsQueueFactory;

class AddOrderPlaceToSnsQueue implements ObserverInterface
{
    /**
     * @var SnsQueueFactory
     */
    protected $snsQueueFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SnsQueueFactory $snsQueueFactory
     * @param SerializerInterface $serializer
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        SnsQueueFactory $snsQueueFactory,
        SerializerInterface $serializer,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->snsQueueFactory = $snsQueueFactory;
        $this->serializer = $serializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $topicArn = $this->config->getOrderPlaceTopicArn();

        if (!$this->config->isEnabled() || empty($topicArn)) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $data = $order->getData();

        $data['items'] = [];
        /* @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            if (!($item->getProductType() == Type::TYPE_SIMPLE && $item->getParentItem())) {
                $data['items'][] = $item->getData();
            }
        }
        $data['addresses'] = [];
        foreach ($order->getAddresses() as $address) {
            $data['addresses'][] = $address->getData();
        }
        $data['payment'] = $order->getPayment()->getMethod();

        /* @var \Vinhcd\AwsSns\Model\SnsQueue $snsQueue */
        $snsQueue = $this->snsQueueFactory->create();
        try {
            $snsQueue->setTopicArn($topicArn);
            $snsQueue->setMessage($this->serializer->serialize($data));
            $snsQueue->save();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
