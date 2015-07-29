<?php

/**
 * Referral rate collection
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Model_Resource_Reward_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('itanium_referral/reward_rate');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Itanium_Referral_Model_Resource_Reward_Rate_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteId = array_merge((array)$websiteId, array(0));
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteId);
        return $this;
    }
}
