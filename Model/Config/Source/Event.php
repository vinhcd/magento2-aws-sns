<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

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
