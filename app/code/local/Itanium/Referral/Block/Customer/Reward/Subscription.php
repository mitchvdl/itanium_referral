<?php
/**
 * Referral Points Settings form
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Customer_Reward_Subscription extends Mage_Core_Block_Template
{
    /**
     * Getter for RewardUpdateNotification
     *
     * @return bool
     */
    public function isSubscribedForUpdates()
    {
        return (bool)$this->_getCustomer()->getData('referral_update_notification');
    }

    /**
     * Getter for RewardWarningNotification
     *
     * @return bool
     */
    public function isSubscribedForWarnings()
    {
        return (bool)$this->_getCustomer()->getData('itanium_referral_warn_not');
    }

    /**
     * Retrieve customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
