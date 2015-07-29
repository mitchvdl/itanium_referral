<?php
/**
 * Customer My Account -> Reward Points container
 *
 * @category    Itanium
 * @package     Itanium_Referral
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Itanium_Referral_Block_Customer_Reward extends Mage_Core_Block_Template
{
    /**
     * Set template variables
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_toHtml();
    }
}
