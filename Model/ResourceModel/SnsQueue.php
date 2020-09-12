<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSns\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SnsQueue extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('vinhcd_aws_sns_queue', 'entity_id');
    }
}
