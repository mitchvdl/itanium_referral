<?php

/**
 * Reward rate edit container
 *
 * @category    Itanium
 * @package     Itanium_Referral
 */
class Itanium_Referral_Block_Adminhtml_Referral_Rate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'rate_id';
        $this->_blockGroup = 'itanium_referral';
        $this->_controller = 'adminhtml_referral_rate';
    }

    /**
     * Getter.
     * Return header text in order to create or edit rate
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_referral_rate')->getId()) {
            return Mage::helper('itanium_referral')->__('Edit Reward Exchange Rate');
        } else {
            return Mage::helper('itanium_referral')->__('New Reward Exchange Rate');
        }
    }

    /**
     * rate validation URL getter
     *
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
}
