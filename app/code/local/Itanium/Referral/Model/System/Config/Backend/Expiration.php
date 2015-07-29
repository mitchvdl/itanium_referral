<?php

/**
 * Backend model for "Referral Points Lifetime"
 *
 */
class Itanium_Referral_Model_System_Config_Backend_Expiration extends Mage_Core_Model_Config_Data
{
    const XML_PATH_EXPIRATION_DAYS = 'itanium_referral/general/expiration_days';

    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return Itanium_Referral_Model_System_Config_Backend_Expiration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = [];
        if ($this->getWebsiteCode()) {
            $websiteIds = [Mage::app()->getWebsite($this->getWebsiteCode())->getId()];
        } else {
            $collection = Mage::getResourceModel('core/config_data_collection')
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', 'websites');
            $websiteScopeIds = [];
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Mage_Core_Model_Website */
                if (!in_array($website->getId(), $websiteScopeIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        }
        if (count($websiteIds) > 0) {
            Mage::getResourceModel('itanium_referral/reward_history')
                ->updateExpirationDate($this->getValue(), $websiteIds);
        }

        return $this;
    }

    /**
     * The same as _beforeSave, but executed when website config extends default values
     *
     * @return Itanium_Referral_Model_System_Config_Backend_Expiration
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getWebsiteCode()) {
            $default = (string)Mage::getConfig()->getNode('default/' . self::XML_PATH_EXPIRATION_DAYS);
            $websiteIds = [Mage::app()->getWebsite($this->getWebsiteCode())->getId()];
            Mage::getResourceModel('itanium_referral/reward_history')
                ->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
