<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSns\Model\ResourceModel\SnsQueue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Vinhcd\AwsSns\Model\SnsQueue::class,
            \Vinhcd\AwsSns\Model\ResourceModel\SnsQueue::class
        );
    }
}
