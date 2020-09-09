<?php

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
