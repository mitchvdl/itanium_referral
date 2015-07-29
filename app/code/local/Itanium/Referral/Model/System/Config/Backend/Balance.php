<?php
/**
 * Backend model for "Referral Points Balance"
 *
 */
class Itanium_Referral_Model_System_Config_Backend_Balance extends Mage_Core_Model_Config_Data
{
    /**
     * Check if max_points_balance >= than min_points_balance
     * (max allowed to RP to gain is more than minimum to redeem)
     *
     * @return Itanium_Referral_Model_System_Config_Backend_Balance
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        if ($this->getFieldsetDataValue('min_points_balance') < 0) {
            Mage::throwException(
                Mage::helper('itanium_referral')->__('"Minimum Reward Points Balance" should be positive number or empty.')
            );
        }
        if ($this->getFieldsetDataValue('max_points_balance') < 0) {
            Mage::throwException(
                Mage::helper('itanium_referral')->__('"Cap Reward Points Balance" should be positive number or empty.')
            );
        }
        if ($this->getFieldsetDataValue('max_points_balance') &&
            ($this->getFieldsetDataValue('min_points_balance') > $this->getFieldsetDataValue('max_points_balance'))) {
            Mage::throwException(
                Mage::helper('itanium_referral')->__('"Minimum Reward Points Balance" should be less or equal to "Cap Reward Points Balance".')
            );
        }
        return $this;
    }
}
