<?php

namespace Vinhcd\AwsSns\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @method string getTopicArn()
 * @method $this setTopicArn(string $value)
 * @method string getMessage()
 * @method $this setMessage(string $value)
 * @method int getCreatedAt()
 */
class SnsQueue extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Vinhcd\AwsSns\Model\ResourceModel\SnsQueue::class);
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->setData('created_at', time());

        return parent::beforeSave();
    }
}
