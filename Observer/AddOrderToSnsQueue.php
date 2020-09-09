<?php

namespace Vinhcd\AwsSns\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSns\Model\SnsQueueFactory;

class AddOrderToSnsQueue implements ObserverInterface
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
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SnsQueueFactory $snsQueueFactory
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        SnsQueueFactory $snsQueueFactory,
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->snsQueueFactory = $snsQueueFactory;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $data = $order->getData();

        $data['items'] = [];
        /* @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            if (!($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE && $item->getParentItem())) {
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
            $snsQueue->setData('topic_arn', $this->scopeConfig->getValue('vinhcd_aws/sns/order_place_arn'));
            $snsQueue->setData('message', $this->serializer->serialize($data));
            $snsQueue->setData('created_at', time());
            $snsQueue->save();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
