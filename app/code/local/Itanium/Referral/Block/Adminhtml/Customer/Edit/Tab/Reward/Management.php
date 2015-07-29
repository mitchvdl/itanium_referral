<?php
/**
 * Referral management container
 *
 * @category    Itanium
 * @package     Itanium_Referral
 
 */
class Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('itanium/referral/customer/edit/management.phtml');
    }

    /**
     * Prepare layout
     *
     * @return Itanium_Referral_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
     */
    protected function _prepareLayout()
    {
        $total = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_management_balance');

        $this->setChild('referral.balance', $total);

        $update = $this->getLayout()
            ->createBlock('itanium_referral/adminhtml_customer_edit_tab_reward_management_update');

        $this->setChild('referral.update', $update);

        return parent::_prepareLayout();
    }
}
