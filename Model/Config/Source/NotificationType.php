<?php

namespace Vinhcd\AwsSns\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;

class NotificationType extends DataObject implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'order_success', 'label' => 'Order Success'],
            ['value' => 'order_failure', 'label' => 'Order Failure'],
            ['value' => 'order_cancel', 'label' => 'Order Cancel'],
        ];
    }
}
