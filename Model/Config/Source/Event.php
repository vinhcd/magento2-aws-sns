<?php

namespace Vinhcd\AwsSns\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;

class Event extends DataObject implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'order_place', 'label' => 'Order Place'],
        ];
    }
}
