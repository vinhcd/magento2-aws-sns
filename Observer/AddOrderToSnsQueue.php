<?php

namespace Vinhcd\AwsSns\Observer;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;

class AddOrderToSnsQueue implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(Observer $observer)
    {
        // TODO: Implement execute() method.
        /** @var $order Order */
        $order = $observer->getData('order');
    }
}
