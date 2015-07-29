<?php

/**
 * Referral collection
 *
 * @category    Itanium
 * @package     Itanium_Referral
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Itanium_Referral_Model_Resource_Reward_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal construcotr
     *
     */
    protected function _construct()
    {
        $this->_init('itanium_referral/reward');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Itanium_Referral_Model_Resource_Reward_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where(is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId);
        return $this;
    }
}
