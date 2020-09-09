<?php

namespace Vinhcd\AwsSns\Observer;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Psr\Log\LoggerInterface;

class AddOrderToSnsQueue implements ObserverInterface
{
    const TOPIC = 'vinhcd.order.place';

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param PublisherInterface $publisher
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $order Order */
        $order = $observer->getData('order');
        $data = $order->getData();

        $data['items'] = [];
        /* @var Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            if (!($item->getProductType() == Type::TYPE_SIMPLE && $item->getParentItem())) {
                $data['items'][] = $item->getData();
            }
        }
        $data['addresses'] = [];
        foreach ($order->getAddresses() as $address) {
            $data['addresses'][] = $address->getData();
        }
        try {
            $this->publisher->publish(self::TOPIC, $this->serializer->serialize($data));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
