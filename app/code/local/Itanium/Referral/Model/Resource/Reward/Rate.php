<?php

/**
 * Referral rate resource model
 *
 * @category    Itanium
 * @package     Itanium_Referral
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Itanium_Referral_Model_Resource_Reward_Rate extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('itanium_referral/reward_rate', 'rate_id');
    }

    /**
     * Fetch rate customer group and website
     *
     * @param Itanium_Referral_Model_Reward_Rate $rate
     * @param int $customerGroupId
     * @param int $websiteId
     * @param int $direction
     * @return Itanium_Referral_Model_Resource_Reward_Rate
     */
    public function fetch(Itanium_Referral_Model_Reward_Rate $rate, $customerGroupId, $websiteId, $direction)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('website_id IN (:website_id, 0)')
            ->where('customer_group_id IN (:customer_group_id, 0)')
            ->where('direction = :direction')
            ->order('customer_group_id DESC')
            ->order('website_id DESC')
            ->limit(1);

        $bind = array(
            ':website_id'        => (int)$websiteId,
            ':customer_group_id' => (int)$customerGroupId,
            ':direction'         => $direction
        );

        $row = $this->_getReadAdapter()->fetchRow($select, $bind);
        if ($row) {
            $rate->addData($row);
        }

        $this->_afterLoad($rate);
        return $this;
    }

    /**
     * Retrieve rate data bu given params or empty array if rate with such params does not exists
     *
     * @param integer $websiteId
     * @param integer $customerGroupId
     * @param integer $direction
     * @return array
     */
    public function getRateData($websiteId, $customerGroupId, $direction)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('website_id = :website_id')
            ->where('customer_group_id = :customer_group_id')
            ->where('direction = :direction');
        $bind = array(
            ':website_id'        => (int)$websiteId,
            ':customer_group_id' => (int)$customerGroupId,
            ':direction'         => $direction
        );
        $data = $this->_getReadAdapter()->fetchRow($select, $bind);
        if ($data) {
            return $data;
        }

        return array();
    }
}
